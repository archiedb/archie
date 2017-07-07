<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Feature extends database_object { 

	public $uid; 
  public $site; // FK Site
  public $catalog_id; // Per site Unique value
  public $lsg_unit; // Lithostratagraphic Unit
  public $level; // FK of Level.uid
  public $keywords;
  public $description;
  public $user; // FK User
  public $image; // Primary Image (FK) 
  public $created;
  public $updated;
  public $closed;
  public $closed_date;
  public $record; // Displayed as F-${catalog_id}
  public $closed_user; // FK User

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid) OR !$uid) { return false; } 

		$row = $this->get_info($uid,'feature'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

    $this->record = 'F-' . $this->catalog_id;
    $this->user = new User($this->user);
    $this->site = new Site($this->site);
    $this->level = new Level($this->level);
    $this->lsg_unit = new Lsgunit($this->lsg_unit);

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

    $sql = 'SELECT * FROM `feature` WHERE `feature`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('feature',$row['uid'],$row); 
      $users[$row['user']] = $row['user'];
    }

    User::build_cache($users);

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		parent::remove_from_cache('feature',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Output friendly stuff
   */
  public function _display($variable) { 



  } // _display

  /**
   * set_primary_image
   * Sets the Primary image for the feature, takes a media.uid and
   * sets feature.image to it
   */
  public function set_primary_image($image_uid) {

    // Make sure it's an image
    $images = Content::feature($this->uid,'image');

    // Not in the current list of images, walk away
    if (!in_array($image_uid,$images)) {
      Err::add('Image','Selected Feature image not currently assoicated with feature');
      return false;
    }

    $sql = "UPDATE `feature` SET `image`=? WHERE `uid`=?";
    $retval = Dba::write($sql,array($image_uid,$this->uid));

    if ($retval) {
      $this->refresh();
    }

    return true;

  } // set_primary_image

  /**
   * update
   * Update an existing feature, this is only related to the description and keywords
   * this doesn't deal with the location/media information
   */
  public function update($input) { 

    Err::clear();

    if (!Feature::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    $uid          = $input['feature_id'];
    $description  = $input['description'];
    $keywords     = $input['keywords'];
    $updated      = time();
    $level        = empty($input['level']) ? NULL : $input['level'];
    $lsg_unit     = empty($input['lsg_unit']) ? NULL : $input['lsg_unit'];

    $sql = "UPDATE `feature` SET `updated`=?, `keywords`=?, `description`=?, `level`=?, `lsg_unit`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array($updated,$keywords,$description,$level,$lsg_unit,$uid)); 

    if (!$db_results) { 
      Err::add('general','Unable to update Feature - please see error log');
      return false;
    }

    $this->refresh();
    $record = $this->record;
    $log_json = json_encode(array('Description'=>$description,'Keywords'=>$keywords,'Level'=>$level,'LSGUnit'=>$lsg_unit,'User'=>\UI\sess::$user->username,'Updated'=>date('r',$updated)));
    Event::record('feature::update',$log_line);

    return true;

  } // update

  /**
   * create
   * Create a new Feature, this also has to insert the initial spatial location
   */
  public static function create($input) { 

    Err::clear();

    // Force the site to the current users site
    $input['site'] = \UI\sess::$user->site->uid;

    if (!Feature::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    // Start the transaction
    if (!Dba::begin_transaction()) {
      Err::add('general','Unable to start DB Transaction, please try again');
      return false;
    }

    if (!isset($input['catalog_id'])) {
      $catalog_sql = "SELECT `catalog_id` FROM `feature` WHERE `site`=? ORDER BY `catalog_id` DESC LIMIT 1 FOR UPDATE";
      $db_results = Dba::read($catalog_sql,array($input['site']));
      $row = Dba::fetch_assoc($db_results);
      $input['catalog_id'] = $row['catalog_id']+1;
    }
    else { 
      $catalog_sql = "SELECT `catalog_id` FROM `feature` WHERE `site`=? AND `catalog_id`=? LIMIT 1 FOR UPDATE";
      $db_results = Dba::read($catalog_sql,array($input['site'],$input['catalog_id']));
      $row = Dba::fetch_assoc($db_results);
      if ($row['catalog_id']) {
        Err::add('general','Duplicate Feature ID - ' . $catalog_id);
        Dba::commit();
        return false;
      }
    } // else

    // Now it's safe to insert it
    $description  = empty($input['description']) ? NULL : $input['description'];
    $keywords     = empty($input['keywords']) ? NULL : $input['keywords'];
    $level        = empty($input['level']) ? NULL : $input['level'];
    $lsg_unit     = empty($input['lsg_unit']) ? NULL : $input['lsg_unit'];
    $created      = time();
    $sql = "INSERT INTO `feature` (`site`,`catalog_id`,`description`,`keywords`,`level`,`lsg_unit`,`user`,`created`) VALUES (?,?,?,?,?,?,?,?)";
    $db_results = Dba::write($sql,array($input['site'],$input['catalog_id'],$description,$keywords,$level,$lsg_unit,\UI\sess::$user->uid,$created));

    if (!$db_results) { 
      Error:add('general','Unknown Error - inserting feature into database');
      $retval = Dba::rollback();
      if (!$retval) { Err::add('general','Unable to roll database changes back, please report this to your Administrator'); }
      Dba::commit();
      return false;
    }

    $insert_id = Dba::insert_id();

    $log_json = json_encode(array('Site'=>$input['site'],'Catalog ID'=>$input['catalog_id'],'Description'=>$input['description'],'Keywords'=>$input['keywords'],'Level'=>$input['level'],'LSGUnit'=>$input['lsg_unit'],'User'=>\UI\sess::$user->username,'Created'=>$created));
    Event::record('feature::create',$log_json);
    
    // Now we add the initial spatial data
    $spatialdata = SpatialData::create(array('record'=>$insert_id,'type'=>'feature','station_index'=>$input['initial_rn'],'northing'=>$input['northing'],
                      'easting'=>$input['easting'],'elevation'=>$input['elevation']));

    if (!$spatialdata) { 
      Err::add('general','Error inserting Spatial Information - please contact your administrator');
    }

    if (!Dba::commit()) {
      Event::record('Dba::commit','Commit Failure - unable to close transaction');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * delete
   * Delete this object
   */
  public function delete() { 

    // remove the spatial data
    if (!SpatialData::delete_by_record($this->uid,'feature')) { 
      Event::error('Feature','Unable to delete Spatial data for [ ' . $this->uid . ' ] aborting feature delete');
      return false; 
    }

    $uid = Dba::escape($this->uid); 
    $sql = "DELETE FROM `feature` WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return true; 

  } // delete

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if (!Field::notempty($input['description'])) {
      Err::add('description','Required field');
    }
    if (!Field::notempty($input['keywords'])) {
      Err::add('keywords','Required field');
    }

    // If RN then no others
    if (strlen($input['initial_rn']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Err::add('initial_rn','Initial RN and North/East/Elevation can not be specified at the same time');
      if (!Field::validate('rn',$input['initial_rn'])) {
        Err::add('initial_rn','Must be numeric');
      }

    }
    // If no RN then all others - unless we have a feature_id
    if (!$input['feature_id'] AND strlen($input['initial_rn']) == 0 AND (!strlen($input['easting']) OR !strlen($input['northing']) OR !strlen($input['elevation']))) {
      Err::add('general','Northing, Easting and Elevation are all required if no Initial RN set');
      if (!strlen($input['easting'])) {
        Err::add('easting','Easting Required');
      }
      if (!strlen($input['northing'])) {
        Err::add('northing','Northing Required');
      }
      if (!strlen($input['elevation'])) {
        Err::add('elevation','Elevation Required');
      }
      if (!Field::validate('northing',$input['northing'])) {
        Err::add('northing','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('easting',$input['easting'])) {
        Err::add('easting','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('elevation',$input['elevation'])) {
        Err::add('easting','Must be numeric and rounded to three decimals');
      }
    } // if No RUN specified

    // Make sure the RN isn't duplicated for this site. 
    $input['rn'] = $input['initial_rn'];
    if (!SpatialData::is_site_unique($input,$input['feature_id'])) {
      Err::add('initial_rn','Duplicate RN in this site');
    }

    if (strlen($input['level'])) {
      $level = new Level($input['level']);
      if (!$level->catalog_id) {
        Err::add('level','Invalid Level Selected');
      }
    }

    if (!Lsgunit::is_valid($input['lsg_unit'])) {
      Err::add('lsg_unit','Invalid Lithostratigraphic Unit');
    }

    if (Err::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * add_point
   * Add a feature spatial_data point
   */
  public function add_point($input) { 

    Err::clear(); 

    $station_index  = isset($input['station_index']) ? $input['station_index'] : NULL;
    $northing       = isset($input['northing']) ? $input['northing'] : NULL;
    $easting        = isset($input['easting']) ? $input['easting'] : NULL;
    $elevation      = isset($input['elevation']) ? $input['elevation'] : NULL;
    $note           = isset($input['note']) ? $input['note'] : NULL;

    if (!$station_index AND !$northing AND !$easting AND !$elevation) { 
      // Well you have to specify something!
      Err::add('general','Nothing entered, doing nothing');
      return false;
    }

    $retval = SpatialData::create(array(
      'record'=>$this->uid,
      'type'=>'feature',
      'station_index'=>$station_index,
      'northing'=>$northing,
      'easting'=>$easting,
      'elevation'=>$elevation,
      'note'=>$note));

    return $retval;

  } //add_point

  /**
   * update_point
   * Update existing point
   */
  public function update_point($input) { 

    Err::clear();

    $station_index  = isset($input['station_index']) ? $input['station_index'] : NULL;
    $northing       = isset($input['northing']) ? $input['northing'] : NULL;
    $easting        = isset($input['easting']) ? $input['easting'] : NULL;
    $elevation      = isset($input['elevation']) ? $input['elevation'] : NULL;
    $note           = isset($input['note']) ? $input['note'] : NULL;

    $point = new SpatialData($input['spatialdata_id']);

    $retval = $point->update(array(
      'spatialdata_id'=>$input['spatialdata_id'],
      'record'=>$this->uid,
      'type'=>'feature',
      'station_index'=>$station_index,
      'northing'=>$northing,
      'easting'=>$easting,
      'elevation'=>$elevation,
      'note'=>$note));

    return $retval;

  } // update_point

  /**
   * del_point
   * Remove a point from the feature
   */
  public function del_point($uid) { 

    $retval = SpatialData::remove($uid);

    return $retval;

  } // del_point

  /**
   * get_uid_from_record
   * Return the UID from the record 
   */
  public static function get_uid_from_record($record,$site='') { 

    if (!$site) { 
      $site = \UI\sess::$user->site->uid;
    }

    // Remove the F- if it's there
    $catalog_id = ltrim($record,'F-');

    $site = Dba::escape($site);
    $catalog_id = Dba::escape($catalog_id); 

    $sql = "SELECT * FROM `feature` WHERE `catalog_id`='$catalog_id' AND `site`='$site'";
    $db_results = Dba::read($sql); 
    $results = Dba::fetch_assoc($db_results);

    if (!isset($results['uid'])) { return false; }

    return $results['uid'];

  } // get_uid_from_record

  /**
   * get_user_features
   * return an array of the last 3 user features (by defualt)
  */
  public static function get_user_features($uid=false,$limit=3) {

    if (!$uid) {
      $uid = \UI\sess::$user->uid;
    }

    $results = array();

    $uid = Dba::escape($uid);
    $limit = abs(floor($limit));
    $sql = "SELECT * FROM `feature` WHERE `user`='$uid' AND `site`=? ORDER BY `created` DESC LIMIT $limit";
    $db_results = Dba::read($sql,array(\UI\sess::$user->site->uid));

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid'];
      parent::add_to_cache('feature',$row['uid'],$row);
    }

    return $results;

  } // get_user_features

  /**
   * has_records
   * Check if this feature has associated records
   */
  public function has_records() {

    $sql = "SELECT COUNT(`uid`) AS `count` FROM `record` WHERE `feature`=? AND `site`=?";
    $db_results = Dba::read($sql,array($this->uid,$this->site->uid));

    $results = Dba::fetch_assoc($db_results);

    if ($results['count'] > 0) { 
      return true; 
    }

    return false; 

  } // has_records

  /**
   * get_records
   * Return an array of the records assoicated with this feature
   */
  public function get_records() { 

    $return = array();

    $sql = "SELECT `uid` FROM `record` WHERE `feature`=? AND `site`=?";
    $db_results = Dba::read($sql,array($this->uid,$this->site->uid));


    while ($results = Dba::fetch_assoc($db_results)) {
      $return[] = $results['uid'];
    }

    return $return; 


  } // get_records

} // end feature level
?>
