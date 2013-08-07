<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Level extends database_object { 

	public $uid; 
  public $site;
  public $record; // UID generated and written down
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
  public $notes;

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'level'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

    // Build the user object, its useful
    $this->user = new User($this->user);
    $this->quad = new Quad($this->quad);
    $this->lsg_unit = new Lsgunit($this->lsg_unit);
    $this->site = new site($this->site);

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
      parent::add_to_cache('level',$row['uid'],$row); 
    }

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
   * create
   * Create a new level entry
   */
  public static function create($input) { 

    // Reset errors before we do any validation
    Error::clear(); 

    // hardcode first excavator
    $input['excavator_one'] = \UI\sess::$user->uid;

    // Site is determined by session
    $input['site'] = \UI\sess::$user->site->uid;

    // Check the input and make sure we think they gave us 
    // what they should have
    if (!Level::validate($input)) { 
      Error::add('general','Invalid field values please check input');
      return false; 
    }

    $site     = Dba::escape($input['site']); 
    $record   = Dba::escape($input['record']); 
    $unit     = Dba::escape($input['unit']); 
    $quad     = Dba::escape($input['quad']); 
    $lsg_unit = Dba::escape($input['lsg_unit']); 
    $northing = Dba::escape($input['northing']); 
    $easting  = Dba::escape($input['easting']); 
    $elv_nw_start   = Dba::escape($input['elv_nw_start']); 
    $elv_ne_start   = Dba::escape($input['elv_ne_start']); 
    $elv_sw_start   = Dba::escape($input['elv_sw_start']); 
    $elv_se_start   = Dba::escape($input['elv_se_start']); 
    $elv_center_start = Dba::escape($input['elv_center_start']); 
    $excavator_one  = Dba::escape(\UI\sess::$user->uid); 
    $excavator_two  = Dba::escape($input['excavator_two']); 
    $excavator_thee = Dba::escape($input['excavator_three']); 
    $excavator_four = Dba::escape($input['excavator_four']); 
    $user = Dba::escape(\UI\sess::$user->uid);
    $created = time(); 
    
    $sql = "INSERT INTO `level` (`site`,`record`,`unit`,`quad`,`lsg_unit`,`northing`,`easting`,`elv_nw_start`," . 
        "`elv_ne_start`,`elv_sw_start`,`elv_se_start`,`elv_center_start`,`excavator_one`,`excavator_two`," . 
        "`excavator_three`,`excavator_four`,`user`,`created`) VALUES ('$site','$record','$unit','$quad','$lsg_unit','$northing','$easting'," . 
        "'$elv_nw_start','$elv_ne_start','$elv_sw_start','$elv_se_start','$elv_center_start','$excavator_one','$excavator_two', " . 
        "'$excavator_three','$excavator_four','$user','$created')"; 
    $db_results = Dba::write($sql); 

    // If it fails we need to unlock!
    if (!$db_results) { 
      Error::add('general','Unable to insert level, DB error please contact administrator'); 
      return false;
    }

    $insert_id = Dba::insert_id();

    $log_line = "$site,$record,$unit,$quad,$lsg_unit,$northing,$easting,$elv_nw_start,$elv_ne_start," . 
          "$elv_sw_start,$elv_se_start,$elv_center_start,$excavator_one,$excavator_two,$excavator_three," . 
          "$excavator_four," . \UI\sess::$user->username . ",\"" . date('r',$created) . "\"";
    Event::record('LEVEL-ADD',$log_line); 

    return $insert_id; 

  } // create

  /**
   * update
   * Updates an existing record
   */
  public function update($input) { 

    // Reset the error state
    Error::clear();

    // Set closed variable for validation
    $input['closed'] = $this->closed;

    // Site is unchangeable!
    $input['site'] = $this->site->uid;

    if (!Level::validate($input)) { 
      Error::add('general','Invalid field values, please check input');
      return false;
    }

    $uid      = Dba::escape($this->uid); 
    $record   = Dba::escape($input['record']); 
    $unit     = Dba::escape($input['unit']);
    $quad     = Dba::escape($input['quad']); 
    $lsg_unit = Dba::escape($input['lsg_unit']);
    $user     = Dba::escape(\UI\sess::$user->uid);
    $updated  = time();
    $northing = Dba::escape($input['northing']);
    $easting  = Dba::escape($input['easting']);
    $elv_nw_start   = Dba::escape($input['elv_nw_start']);
    $elv_nw_finish  = Dba::escape($input['elv_nw_finish']);
    $elv_ne_start   = Dba::escape($input['elv_ne_start']);
    $elv_ne_finish  = Dba::escape($input['elv_ne_finish']);
    $elv_sw_start   = Dba::escape($input['elv_sw_start']);
    $elv_sw_finish  = Dba::escape($input['elv_sw_finish']);
    $elv_se_start   = Dba::escape($input['elv_se_start']);
    $elv_se_finish  = Dba::escape($input['elv_se_finish']); 
    $elv_center_start = Dba::escape($input['elv_center_start']);
    $elv_center_finish  = Dba::escape($input['elv_center_finish']); 
    $excavator_one  = Dba::escape($input['excavator_one']); 
    $excavator_two  = Dba::escape($input['excavator_two']); 
    $excavator_three  = Dba::escape($input['excavator_three']);
    $excavator_four = Dba::escape($input['excavator_four']); 
    $description    = Dba::escape($input['description']);
    $difference     = Dba::escape($input['difference']);
    $notes          = Dba::escape($input['notes']);

    $sql = "UPDATE `level` SET `record`='$record', `unit`='$unit', `quad`='$quad', `lsg_unit`='$lsg_unit', " . 
          "`user`='$user', `updated`='$updated', `northing`='$northing', `easting`='$easting', " . 
          "`elv_nw_start`='$elv_nw_start', `elv_nw_finish`='$elv_nw_finish', `elv_ne_start`='$elv_ne_start', " . 
          "`elv_ne_finish`='$elv_ne_finish', `elv_sw_start`='$elv_sw_start', `elv_sw_finish`='$elv_sw_finish', " .
          "`elv_se_start`='$elv_se_start', `elv_se_finish`='$elv_se_finish', `elv_center_start`='$elv_center_start', " . 
          "`elv_center_start`='$elv_center_start', `elv_center_finish`='$elv_center_finish', " . 
          "`excavator_one`='$excavator_one', `excavator_two`='$excavator_two', `excavator_three`='$excavator_three', " . 
          "`excavator_four`='$excavator_four', `description`='$description', `difference`='$difference', `notes`='$notes' " . 
          "WHERE `level`.`uid`='$uid' LIMIT 1";
    $retval = Dba::write($sql);

    if (!$retval) { 
      Error::add('database','Database update failed, please contact administrator');
      return false;
    }

    $log_line = "$uid,$record,$unit,$quad,$lsg_unit,$northing,$easting,$elv_nw_start,$elv_nw_finish,$elv_ne_start," .
      "$elv_ne_finish,$elv_sw_start,$elv_sw_finish,$elv_se_start,$elv_se_finish,$elv_center_start," . 
      "$elv_center_finish,$excavator_one,$excavator_two,$excavator_three,$excavator_four," . \UI\sess::$user->username . ",\"" . date('r',$updated) . "\""; 
    Event::record('LEVEL-UPDATE',$log_line);

    // Refresh record
    $this->refresh();

    return true; 

  } // update

  /**
   * set_primary_image
   * Defines which image is the 'level photo'
   */
  public function set_primary_image($image) { 

    // Make sure it's an image that is assoicated with this record
    $images = Content::level($this->uid,'image'); 

    // Not in the current list of images
    if (!in_array($image,$images)) { 
      Error::add('Image','Selected Level image not currently assoicated with level'); 
      return false; 
    }

    $image  = Dba::escape($image); 
    $uid    = Dba::escape($this->uid); 
    $sql = "UPDATE `level` SET `image`='$image' WHERE `uid`='$uid'"; 
    $retval = Dba::write($sql);

    $this->refresh(); 

    return true; 

  } // set_primary_image

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if ($input['closed'] == 1 AND !Access::has('admin','admin',$input['uid'])) {
      Error::add('closed','Level is closed, unable to updated'); 
    }

    if (!$input['record']) { 
      Error::add('level','Required field');
    }
    else {
      // Make sure this isn't a duplicate level
      $record = Dba::escape($input['record']);
      $quad   = Dba::escape($input['quad']); 
      $unit   = Dba::escape($input['unit']); 
      $uid    = Dba::escape($input['uid']); 
      $site   = Dba::escape($intpu['site']); 
      $sql = "SELECT `level`.`uid` FROM `level` WHERE `record`='$record' AND `quad`='$quad' AND `unit`='$unit' AND `site`='$site' AND `uid`<>'$uid'";
      $db_results = Dba::read($sql); 
      $row = Dba::fetch_assoc($db_results); 
      if ($row['uid']) { 
        Error::add('level','Duplicate Level for this Unit and Quad'); 
      }
    }

    if (!is_numeric($input['record'])) { 
      Error::add('level','Level must be numeric');
    }

    // User must have access to this site
    if (!Access::has('site','write',$input['site'])) {
      Error::add('site','Insufficient access to this site');
    }

		// Unit A-Z
		if (preg_match("/[^A-Za-z]/",$input['unit'])) { 
			Error::add('unit','UNIT must be A-Z'); 
		}

		// lsg_unit, numeric less then 50
		if (!in_array($input['lsg_unit'],array_keys(lsgunit::$values)) OR $input['lsg_unit'] > 50 OR $input['lsg_unit'] < 2) { 
			Error::add('lsg_unit','Invalid Lithostratigraphic Unit'); 
		}

		// The quad has to exist
		if (!in_array($input['quad'],array_keys(quad::$values))) { 
			Error::add('quad','Invalid Quad selected'); 
		} 

    // Check the 'start' values 
    $field_check = array('northing','easting','elv_nw_start','elv_ne_start','elv_sw_start','elv_se_start','elv_center_start');

    foreach ($field_check as $field) { 

      if ($input[$field] <= 0 OR round($input[$field],3) != $input[$field]) { 
        Error::add($field,'Must be numeric and rounded to three decimal places'); 
      }

      
      if (!is_numeric($input[$field])) {
        Error::add($field,'Must be numeric');
      }

      // Must be set
      if (!$input[$field]) {
        Error::add($field,'Required field');
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
          Error::add($field,'Must be numeric');
        }

        // Make sure it's not less then zero and has the correct accuracy
        if ($input[$field] < 0 OR round($input[$field],3) != $input[$field]) {
          Error::add($field,'Must be numeric and rounded to three decimal places'); 
        }
        // Make sure it's deeper then the start
        $start_name = substr($field,0,strlen($field)-6) . 'start';
        if ($input[$field] > $input[$start_name]) { 
          Error::add($field,'Must be lower then starting elevation');
        }         
      }

    } // end foreach ends

    $excavator_check = array('excavator_one','excavator_two','excavator_three','excavator_four'); 
    $excavator_count = 0;
    $excavator_exists = array();

    foreach ($excavator_check as $excavator_id) { 

      if ($input[$excavator_id]) {
        
        if (in_array($input[$excavator_id],$excavator_exists)) { 
          Error::add($excavator_id,'Duplicate Excavator, can\'t be in two places at once');
        }

        $user = new User($input[$excavator_id]); 

        if (!$user->username OR $user->disabled) { 
          Error::add($excavator_id,'Excavator unknown or disabled'); 
        }
        else {
          $excavator_exists[] = $input[$excavator_id];
          $excavator_count++;
        }
      }
    } // End foreach

    // We have to have at least one excavator
    if ($excavator_count == 0) { 
      Error::add('excavator_one','At least one excavator must be set');
    }
  
    if (Error::occurred()) { return false; }

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
   * close
   * Attempt to close the level
   */
  public function close($input) { 

    Error::clear();

    // Make sure it's safe to close
    if (!$this->validate_close($input)) {
      Error::add('general','Unable to close level');
      return false;
    }

    $uid = Dba::escape($this->uid); 
    $updated = time(); 
    $user = Dba::escape(\UI\sess::$user->uid);
    $sql = "UPDATE `level` SET `closed`='1',`closed_user`='$user',`closed_date`='$updated' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    if (!$db_results) {
      Error::add('database','Unknown database error, please contact administrator'); 
      return false;
    }

    return true; 

  } // close

  /**
   * validate_close
   * Validate the attempt to close
   */
  public function validate_close($input) { 

    // Make sure it's an admin or an excavator 
    if (\UI\sess::$user->access < 100) { 
      if (!$this->is_excavator()) { 
        Error::add('excavator','Unable to close, you are not an excavator');
      }
    }

    if (!$this->has_photo()) {
      Error::add('photo','No Level photo found');
    }

    if (!$this->questions_answered()) {
      Error::add('questions','Questions not answered'); 
    }

    $checkboxes = array('kroto_sample','kroto_bag','level_photo','notes_done','connect');

    foreach ($checkboxes as $key) { 
      if ($input[$key] != 1) { 
        Error::add($key,'Not completed?'); 
      }
    }

    if (Error::occurred()) {
      return false;
    }

    return true; 

  } // validate_close

} // end class level
?>
