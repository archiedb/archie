<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Level extends database_object { 

	public $uid; 
  public $site;
  public $catalog_id; // Numeric value of item
  public $record; // UID generated and written down (public facing value) SITE-CAT#
  public $name; // UNIT:QUAD:CATALOGID (used for drop-downs to identify the level)
  public $unit;
  public $quad;
  public $lsg_unit;
  public $user; // User who last modified this record
  public $created;
  public $updated;
  public $northing;
  public $easting;
  public $elv_nw_start;
  public $elv_nw_finish;
  public $elv_ne_start;
  public $elv_ne_finish;
  public $elv_sw_start;
  public $elv_sw_finish;
  public $elv_se_start;
  public $elv_se_finish;
  public $elv_center_start;
  public $elv_center_finish;
  public $excavator_one;
  public $excavator_two;
  public $excavator_three;
  public $excavator_four;
  public $description;
  public $difference;
  public $closed;
  public $closed_date;
  public $closed_user; 
  public $image; // primary image for level
  public $notes;
  public $other;
  public $z_order; // Z-order for elevations

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'level'); 

    if (!is_array($row)) { return false; }
		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

    // Build the user object, its useful
    $this->user     = new User($this->user);
    $this->quad     = new Quad($this->quad);
    $this->unit     = new Unit($this->unit);
    $this->lsg_unit = new Lsgunit($this->lsg_unit);
    $this->site     = new Site($this->site);
    $this->record   = 'L-' . $this->catalog_id;
    $this->name     = $this->unit->name . ':' . $this->quad->name . ':' . $this->catalog_id;

		return true; 

	} // constructor

  /**
   * build_cache
   */
  public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `level` WHERE `level`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $sites[$row['site']] = $row['site'];
      parent::add_to_cache('level',$row['uid'],$row); 
    }

    Site::build_cache($sites);

    return true; 


  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('level',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Show the pretty version of things
   * Display function
   */
  public function _display($variable) { 


  } // _display

  /**
   * create
   * Create a new level entry
   */
  public static function create($input) { 

    // Reset errors before we do any validation
    Err::clear(); 

    // If they aren't an admin then hardcode first excavator
    if (!Access::is_admin() OR !$input['excavator_one']) {
      $input['excavator_one'] = \UI\sess::$user->uid;
    }

    // Site is determined by session
    $input['site'] = \UI\sess::$user->site->uid;

    // Check the input and make sure we think they gave us 
    // what they should have
    if (!Level::validate($input)) { 
      Err::add('general','Invalid field values please check input');
      return false; 
    }

    $site             = $input['site']; 
    $unit             = $input['unit']; 
    $quad             = $input['quad']; 
    $lsg_unit         = $input['lsg_unit']; 
    $northing         = $input['northing']; 
    $easting          = $input['easting']; 
    $catalog_id       = $input['catalog_id']; 
    $elv_nw_start     = $input['elv_nw_start']; 
    $elv_ne_start     = $input['elv_ne_start']; 
    $elv_sw_start     = $input['elv_sw_start']; 
    $elv_se_start     = $input['elv_se_start']; 
    $elv_center_start = $input['elv_center_start']; 
    $excavator_one    = $input['excavator_one']; 
    $excavator_two    = strlen($input['excavator_two']) > 0 ? $input['excavator_two'] : NULL;
    $excavator_three  = strlen($input['excavator_three']) > 0 ? $input['excavator_three'] : NULL;
    $excavator_four   = strlen($input['excavator_four']) > 0 ? $input['excavator_four'] : NULL;
    $z_order          = 'desc';
    $user             = \UI\sess::$user->uid;
    $type             = 'level';
    $created          = time(); 
    
    //FIXME: Allow updated to be null in the future
    $sql = "INSERT INTO `level` (`site`,`catalog_id`,`unit`,`quad`,`lsg_unit`,`northing`,`easting`,`elv_nw_start`," . 
        "`elv_ne_start`,`elv_sw_start`,`elv_se_start`,`elv_center_start`,`excavator_one`,`excavator_two`," . 
        "`excavator_three`,`excavator_four`,`user`,`created`,`updated`,`image`,`elv_nw_finish`,`elv_ne_finish`,`elv_sw_finish`,`elv_se_finish`,`elv_center_finish`,`z_order`,`type`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
    $db_results = Dba::write($sql,array($site,$catalog_id,$unit,$quad,$lsg_unit,$northing,$easting,$elv_nw_start,$elv_ne_start,$elv_sw_start,$elv_se_start,$elv_center_start,$excavator_one,$excavator_two,$excavator_three,$excavator_four,$user,$created,0,0,0,0,0,0,0,$z_order,$type)); 

    if (!$db_results) { 
      Err::add('general','Unable to insert level, DB error please contact administrator'); 
      return false;
    }

    $insert_id = Dba::insert_id();

    $log_line = json_encode(array('site'=>$site,'Catalog ID'=>$catalog_id,'Unit'=>$unit,'Quad'=>$quad,'LSG-Unit'=>$lsg_unit,'Northing'=>$northing,
          'Easting'=>$easting,'Elv-NW-S'=>$elv_nw_start,'Elv-NE-S'=>$elv_ne_start,'Elv-SW-S'=>$elv_sw_start,'Elv-SE-S'=>$elv_se_start,
          'Elv-Cent-S'=>$elv_center_start,'Exc-one'=>$excavator_one,'Exc-two'=>$excavator_two,'Exc-three'=>$excavator_three,'Exc-four'=>$excavator_four,
          'User'=>\UI\sess::$user->username,'Date'=>date('r',$created),'Z-order'=>$z_order,'Type'=>$type));
    Event::record('level::create',$log_line); 

    return $insert_id; 

  } // create

  /**
   * update
   * Updates an existing record
   */
  public function update($input) { 

    // Reset the error state
    Err::clear();

    // Set closed variable for validation
    $input['closed'] = $this->closed;

    // Site is unchangeable!
    $input['site'] = $this->site->uid;
    $input['level_id'] = $this->uid;

    if (!Level::validate($input)) { 
      Err::add('general','Invalid field values, please check input');
      return false;
    }

    $uid          = $this->uid; 
    $catalog_id   = $input['catalog_id']; 
    $unit         = $input['unit'];
    $quad         = $input['quad']; 
    $lsg_unit     = $input['lsg_unit'];
    $user         = \UI\sess::$user->uid;
    $updated      = time();
    $northing     = $input['northing'];
    $easting      = $input['easting'];
    $elv_nw_start   = $input['elv_nw_start'];
    $elv_nw_finish  = $input['elv_nw_finish'];
    $elv_ne_start   = $input['elv_ne_start'];
    $elv_ne_finish  = $input['elv_ne_finish'];
    $elv_sw_start   = $input['elv_sw_start'];
    $elv_sw_finish  = $input['elv_sw_finish'];
    $elv_se_start   = $input['elv_se_start'];
    $elv_se_finish  = $input['elv_se_finish']; 
    $elv_center_start   = $input['elv_center_start'];
    $elv_center_finish  = $input['elv_center_finish']; 
    $excavator_one    = empty($input['excavator_one']) ? NULL : $input['excavator_one'];
    $excavator_two    = empty($input['excavator_two']) ? NULL : $input['excavator_two'];
    $excavator_three  = empty($input['excavator_three']) ? NULL : $input['excavator_three'];
    $excavator_four   = empty($input['excavator_four']) ? NULL : $input['excavator_four'];
    $description      = $input['description'];
    $difference       = $input['difference'];
    $notes            = $input['notes'];
    $other            = $input['other'];
    $z_order          = 'desc';

    $sql = "UPDATE `level` SET `catalog_id`=?,`unit`=?,`quad`=?,`lsg_unit`=?,`user`=?,`updated`=?," .
          "`northing`=?,`easting`=?,`elv_nw_start`=?, `elv_nw_finish`=?, `elv_ne_start`=?,`elv_ne_finish`=?,".
          "`elv_sw_start`=?, `elv_sw_finish`=?,`elv_se_start`=?,`elv_se_finish`=?,`elv_center_start`=?," . 
          "`elv_center_finish`=?,`excavator_one`=?, `excavator_two`=?, `excavator_three`=?,`excavator_four`=?,".
          "`description`=?, `difference`=?, `notes`=? ,`z_order`=?, `other`=? WHERE `level`.`uid`=? LIMIT 1"
    $retval = Dba::write($sql,array($catalog_id,$unit,$quad,$lsg_unit,$user,$updated,$northing,$easting,$elv_nw_start,
      $elv_nw_finish,$elv_ne_start,$elv_ne_finish,$elv_sw_start,$elv_sw_finish,$elv_se_start,$elv_se_finish,$elv_center_start,
      $elv_center_finish,$excavator_one,$excavator_two,$excavator_three,$excavator_four,$description,$difference,$notes,$z_order,$other,$uid));

    if (!$retval) { 
      Err::add('database','Database update failed, please contact administrator');
      return false;
    }
    $log_line = json_encode(array('site'=>$site,'Catalog ID'=>$catalog_id,'Unit'=>$unit,'Quad'=>$quad,'LSG-Unit'=>$lsg_unit,'Northing'=>$northing,
          'Easting'=>$easting,'Elv-NW-S'=>$elv_nw_start,'Elv-NE-S'=>$elv_ne_start,'Elv-SW-S'=>$elv_sw_start,'Elv-SE-S'=>$elv_se_start,
          'Elv-Cent-S'=>$elv_center_start,'Exc-one'=>$excavator_one,'Exc-two'=>$excavator_two,'Exc-three'=>$excavator_three,'Exc-four'=>$excavator_four,
          'User'=>\UI\sess::$user->username,'Date'=>date('r',$created),'Z-order'=>$z_order,'Desc'=>$description,'Diff'=>$difference,'Notes'=>$notes,'General'=>$other,'Type'=>$this->type));
    Event::record('level::create',$log_line); 

    // Refresh record
    $this->refresh();

    return true; 

  } // update

  /**
   * delete
   * This deletes a level
  */
  public function delete() { 

    // Delete any content first
    $images = Content::level($this->uid,'image');
    foreach ($images as $uid) { 
      $image = new Content($uid,'image','level');
      $image->delete();
    }
    $models = Content::level($this->uid,'3dmodel');
    foreach ($models as $uid) { 
      $model = new Content($uid,'3dmodel','level');
      $model->delete();
    }
    $others = Content::level($this->uid,'media');
    foreach ($others as $uid) { 
      $other = new Content($uid,'media','level');
      $other->delete();
    }

    $uid = Dba::escape($this->uid);

    $sql = "DELETE FROM `level` WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return true;

  } // delete

  /**
   * set_primary_image
   * Defines which image is the 'level photo'
   */
  public function set_primary_image($image) { 

    // Make sure it's an image that is assoicated with this record
    $images = Content::level($this->uid,'image'); 

    // Not in the current list of images
    if (!in_array($image,$images)) { 
      Err::add('Image','Selected Level image not currently assoicated with level'); 
      return false; 
    }

    $sql = "UPDATE `level` SET `image`=? WHERE `uid`=?"; 
    $retval = Dba::write($sql,array($image,$this->uid));

    if ($retval) {
      $this->refresh(); 
    }

    return true; 

  } // set_primary_image

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    // If we've got an existing record
    if (!empty($input['level_id'])) {
      $level = new Level($input['level_id']);
    }

    // If closed is specified
    if (isset($input['closed'])) {
      if ($input['closed'] == 1 AND !Access::is_admin()) {
        Err::add('closed','Level is closed, unable to updated'); 
      }
    }

    // Catalog ID must be numeric, and exist
    if (!Field::validate('catalog_id',$input['catalog_id'])) {
      Err::add('level','Must be numeric');
    }

    // Make sure this isn't a duplicate level, filter on UID if passed
    if (isset($input['catalog_id'])) {
      $uid_sql    = isset($input['uid']) ? "AND `uid`<>'" . Dba::escape($input['uid']) . "'" : '';

      $sql = "SELECT `level`.`uid` FROM `level` WHERE `catalog_id`=? AND `quad`=? AND `unit`=? AND `site`=? $uid_sql";
      $db_results = Dba::read($sql,array($input['catalog_id'],$input['quad'],$input['unit'],$input['site'])); 
      $row = Dba::fetch_assoc($db_results); 
      if ($row['uid']) { 
        Err::add('level','Duplicate Level for this Unit and Quad'); 
      }
    }

		// Unit A-Z
    if (empty($input['level_id'])) {
  		if (!Unit::is_valid($input['unit'])) { 
  			Err::add('unit','UNIT specified not valid'); 
  		}
      if (!Lsgunit::is_valid($input['lsg_unit'])) {
        Err::add('lsg_unit','Invalid LU');
      }
      if (!Quad::is_valid($input['quad'])) {
        Err::add('quad','Invalid Quad selected');
      }
    }
    else {
      if (!Unit::is_valid($input['unit']) AND $input['unit'] != $level->unit->name) {
        Err::add('unit','Unit specified not valid');
      }
      if (!Lsgunit::is_valid($input['lsg_unit']) AND $input['lsg_unit'] != $level->lsg_unit->name) {
        Err::add('lsg_unit','Invalid LU');
      }
      if (!Quad::is_valid($input['quad']) AND $input['quad'] != $level->quad->name) {
        Err::add('quad','Invalid Quad selected');
      }
    }

    // Check the 'start' values 
    $field_check = array('northing','easting','elv_nw_start','elv_ne_start','elv_sw_start','elv_se_start','elv_center_start');
    //FIXME: This should use field::validate()
    foreach ($field_check as $field) { 

      // Must be set
      if (!isset($input[$field])) {
        Err::add($field,'Required field');
        continue;
      }
      if (!is_numeric($input[$field])) {
        Err::add($field,'Must be numeric');
        continue;
      }
      if ($input[$field] < 0 OR round($input[$field],3) != $input[$field]) { 
        Err::add($field,'Must be numeric and rounded to three decimal places'); 
      }
    } // end foreach starts 

    // Check the 'end' values
    $field_check = array('elv_nw_finish','elv_ne_finish','elv_sw_finish','elv_se_finish','elv_center_finish'); 
    
    foreach ($field_check as $field) { 

      // if they aren't set, we don't care
      if (isset($input[$field])) {
        // If its empty then we can ignore
        if ($input[$field] == '') { continue; }

        if (!is_numeric($input[$field])) {
          Err::add($field,'Must be numeric');
          continue;
        }

        // Make sure it's not less then zero and has the correct accuracy
        if ($input[$field] < 0 OR round($input[$field],3) != $input[$field]) {
          Err::add($field,'Must be numeric and rounded to three decimal places'); 
        }
        // Make sure it's deeper then the start
        $start_name = substr($field,0,strlen($field)-6) . 'start';
        if ($input[$field] > $input[$start_name]) { 
          Err::warning($field,'Expected to be lower than start');
        }         
      }

    } // end foreach ends

    $excavator_check = array('excavator_one','excavator_two','excavator_three','excavator_four'); 
    $excavator_count = 0;
    $excavator_exists = array();

    foreach ($excavator_check as $excavator_id) { 

      if ($input[$excavator_id]) {
        
        if (in_array($input[$excavator_id],$excavator_exists)) { 
          Err::add($excavator_id,'Duplicate Excavator, can\'t be in two places at once');
        }

        $user = new User($input[$excavator_id]); 

        // Allow administrative users to select disabled/messed up excavators
        if ((!$user->username OR $user->disabled) AND !Access::is_admin()) { 
          Err::add($excavator_id,'Excavator unknown or disabled'); 
        }
        else {
          $excavator_exists[] = $input[$excavator_id];
          $excavator_count++;
        }
      }
    } // End foreach

    // We have to have at least one excavator
    if ($excavator_count == 0) { 
      Err::add('excavator_one','At least one excavator must be set');
    }
  
    if (Err::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * is_excavator
   * Check if the specified user is an excavator 
   */
  public function is_excavator($uid) { 

    $fields = array('excavator_one','excavator_two','excavator_three','excavator_four'); 

    foreach ($fields as $excavator) { 
      if ($this->$excavator == $uid) { return true; }
    }

    return false; 

  } // is_excavator

  /**
   * questions answered
   * Make sure they've put something in the two questions
   */
  public function questions_answered() {

    if (!strlen($this->description)) { return false; }
    if (!strlen($this->difference)) { return false; }

    return true; 

  } // questions_answered

  /**
   * has_photo
   * make sure there's at least one photo for this level
   */
  public function has_photo() {

    $images = Content::level($this->uid,'image');

    if (!count($images)) { return false; }
    
    // The primary image must be in the set of images returned, and exist :)
    if (!in_array($this->image,$images)) { return false; }

    return true;   

  } // has_photo

  /**
   * has_records
   * this makes sure the level has records
   */
  public function has_records() { 

    $uid = Dba::escape($this->uid); 

    $sql = "SELECT COUNT(`uid`) AS `count` FROM `record` WHERE `level`='$uid'";
    $db_results = Dba::read($sql); 

    $results = Dba::fetch_assoc($db_results);

    if ($results['count'] > 0) { return true; }

    return false; 

  } // has_records

  /**
   * records
   * Return an array of all of the records in this level
   */
  public function records() { 

    $level  = Dba::escape($this->uid); 

    $sql = "SELECT `record`.`uid` FROM `record` WHERE `record`.`level`='$level'"; 
    $db_results = Dba::read($sql); 

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid']; 
    }

    return $results; 

  } // records

  /**
   * open
   * Re-open the level
   */
  public function open() { 

    // Really not much to do here
    $uid = Dba::escape($this->uid);

    $sql = "UPDATE `level` SET `closed` = NULL, `closed_date` = NULL, `closed_user` = NULL WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    $log_line = "Level " . $this->catalog_id . " re-opened in site " . $this->site->name;
    Event::record('LEVEL-REOPEN',$log_line);

    return true; 

  } // open

  /**
   * close
   * Attempt to close the level
   */
  public function close($input) { 

    Err::clear();

    // Make sure it's safe to close
    if (!$this->validate_close($input)) {
      Err::add('general','Unable to close level');
      return false;
    }

    $uid = Dba::escape($this->uid); 
    $updated = time(); 
    $user = Dba::escape(\UI\sess::$user->uid);
    $sql = "UPDATE `level` SET `closed`='1',`closed_user`='$user',`closed_date`='$updated' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    if (!$db_results) {
      Err::add('database','Unknown database error, please contact administrator'); 
      return false;
    }

    $this->refresh();

    return true; 

  } // close

  /**
   * validate_close
   * Validate the attempt to close
   */
  public function validate_close($input) { 

    // Make sure it's an admin or an excavator 
    if (!$this->is_excavator(\UI\sess::$user->uid) AND !Access::has('level','manage')) { 
        Err::add('excavator','Unable to close, you are not a site manager');
    }

    if (!$this->has_photo()) {
      Err::add('photo','No Level photo found');
    }

    if (!$this->questions_answered()) {
      Err::add('questions','Questions not answered'); 
    }

    $checkboxes = array('kroto_sample','kroto_bag','level_photo','notes_done','connect');

    foreach ($checkboxes as $key) { 
      if ($input[$key] != 1) { 
        Err::add($key,'Not completed?'); 
      }
    }

    if (Err::occurred()) {
      return false;
    }

    return true; 

  } // validate_close

  /**
   * get_uid_from_record
   * Take the record and current site and return the UID (if it exists)
   */
  public static function get_uid_from_record($catalog_id,$quad,$unit,$site='') {

    if (!$site) {
      $site = \UI\sess::$user->site->uid;
    }

    $site = Dba::escape($site);
    $quad = Dba::escape($quad);
    $unit = Dba::escape($unit);
    $catalog_id = Dba::escape($catalog_id);
    $sql = "SELECT * FROM `level` WHERE `site`='$site' AND `quad`='$quad' AND `unit`='$unit' AND `catalog_id`='$catalog_id'";
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results); 

    if (!isset($row['uid'])) { 
      return false;
    }

    // Cache it
    parent::add_to_cache('level',$row['uid'],$row);

    return $row['uid'];

  } // get_uid_from_record

  /**
   * get_open_user_levels
   * return the levels for specified user, default to this
   */
  public static function get_open_user_levels($user_uid='') { 

    if (!$user_uid) {
      $user_uid = \UI\sess::$user->uid;
    }

    $results = array();

    $user = Dba::escape($user_uid);
    $site = Dba::escape(\UI\sess::$user->site->uid);
    if (Access::is_admin()) { 
      $sql = "SELECT * FROM `level` WHERE `closed` IS NULL and `site`='$site' ORDER BY `unit` ASC";
    }
    else {
      $sql = "SELECT * FROM `level` WHERE (`excavator_one`='$user' OR `excavator_two`='$user' OR `excavator_three`='$user' OR `excavator_four`='$user') AND `closed` IS NULL AND `site`='$site' ORDER BY `unit` ASC";
    }
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid'];
      $users[$row['user']] = $row['user'];
      if ($row['excavator_one']) { $users[$row['excavator_one']] = $row['excavator_one']; }
      if ($row['excavator_two']) { $users[$row['excavator_two']] = $row['excavator_two']; }
      if ($row['excavator_three']) { $users[$row['excavator_three']] = $row['excavator_three']; }
      if ($row['excavator_four']) { $users[$row['excavator_four']] = $row['excavator_four']; }
      parent::add_to_cache('level',$row['uid'],$row);
    }

    User::build_cache($users);

    return $results;

  } // get_open_user_levels

  /**
   * get_open_levels
   * Return an array of the currently open levels for this site
   */
  public static function get_open_levels() { 

    $results = array(); 

    $site = Dba::escape(\UI\sess::$user->site->uid);


    $sql = "SELECT * FROM `level` WHERE `closed`='0' AND `site`='$site'";
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid'];
      parent::add_to_cache('level',$row['uid'],$row);
    }

    return $results;

  } // get_open_levels
  
} // end class level
?>
