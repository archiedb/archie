<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Level extends database_object { 

	public $uid; 

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'level'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

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

    // Check the input and make sure we think they gave us 
    // what they should have
    if (!Level::validate($input)) { 
      Error::add('general','Invalid field values please check input');
      return false; 
    }

    $retval = false; 
    $times = 0; 
    $lock_sql = "LOCK TABLES `level` WRITE";
    $unlock_sql = "UNLOCK TABLES";

    // Itterate five times trying to lock the tables before giving up
    while (!$retval && $times < 5) { 

      $retval = Dba::read($lock_sql) ? true : false;

      if (!$retval) { $times++; sleep(1); }

    }

    // If we never get the lock then bail
    if (!$retval) { 
      Error::add('general','Unable to establish Database Lock, retry'); 
      return false; 
    }

    // Init row array
    $row = array(); 

    $site = Dba::escape(Config::get('site')); 
    $sql = "SELECT `record` FROM `level` WHERE `site`='$site' AND ORDER BY `record` DESC LIMIT 1"; 
    $db_results = Dba::read($sql);
    $row = Dba::fetch_assoc($db_results);
    Dba::finish($db_results); 

    $record = $row['record']+1; 

    $site     = Dba::escape(Config::get('site')); 
    $record   = Dba::escape($record); 
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
    $excavator_one  = Dba::escape($input['excavator_one']); 
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
      Dba::write($unlock_sql); 
      Error::add('general','Unable to insert level, DB error please contact administrator'); 
      return false;
    }

    $insert_id = Dba::insert_id();

    // Release the table
    Dba::write($unlock_sql); 

    $log_line = "$site,$record,$unit,$quad,$lsg_unit,$northing,$easting,$elv_nw_start,$elv_ne_start," . 
          "$elv_sw_start,$elv_se_start,$elv_center_start,$excavator_one,$excavator_two,$excavator_three," . 
          "$excavator_four," . \UI\sess::$user->username . ",\"" . date('r',$created) . "\"";
    Event::record('LEVEL-ADD',$log_line); 

    return $insert_id; 

  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    // Site - set by config
    // record_id 
    //   K-????? for Krotovina
    //   F-????? for feature
    //   ??? for Level
    // quad - NW/NE (list)
    // unit - A-Z (list)
    // northing - 8,3 decimal
    // easting - ''
    // type - Feature, Krotovina, Level
    // elv_* are same as northing
    
    if (Error::occurred()) { return false; }

    return true; 

  } // validate

} // end class level
?>
