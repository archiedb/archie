<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Feature extends database_object { 

	public $uid; 
  public $site; // FK Site
  public $record; // FK Record
  public $keywords;
  public $description;
  public $user; // FK User
  public $created;
  public $updated;
  public $closed;
  public $closed_date;
  public $closed_user; // FK User

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'feature'); 

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

    $sql = 'SELECT * FROM `feature` WHERE `feature`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('feature',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('feature',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * create
   */
  public static function create($input) { 

    Error::clear();

    // Force the site to the current users site
    $input['site'] = \UI\sess::$user->site->uid;

    if (!Feature::validate($input)) {
      Error::add('general','Invalid Field Values - please check input');
      return false;
    }

    // Record (id for bag) is generated here, lock tables time!
    $db_results = false;
    $times = 0;
    $lock_sql = "LOCAL TABLES `feature` WRITE;";
    $unlock_sql = "UNLOCK TABLES";

    // Only wait 3 seconds for this, shouldn't take that long
    while (!$db_results && $times < 3) { 
      // If we make it this far we're good to go
      $db_results = Dba::write($lock_sql);

      if (!$db_results) { sleep(1); $times++; }

    } // end while

    // If we didn't get the lock, bail
    if (!$db_results) {
      Error::add('general','Database Read Failure, please resubmit');
      return false;
    }

    if ($input['catalog_id']) {
      $site = Dba::escape($input['site']);
      $catalog_sql = "SELECT `catalog_id` FROM `feature` WHERE `site`='$site' ORDER BY `catalogId` DESC LIMIT 1";
      $db_results = Dba::read($catalog_sql);
      $row = Dba::fetch_assoc($db_results);
      Dba::finish($db_results);

      $input['catalog_id'] = $row['catalog_id']+1;
    }
    else { 
      $site = Dba::escape($input['site']);
      $catalog_id = Dba::escape($input['catalog_id']);
      $catalog_sql = "SELECT `catalog_id` FROM `feature` WHERE `site`='$site' AND `catalog_id`='$catalog_id' LIMIT 1";
      $db_results = Dba::read($catalog_sql);
      $row = Dba::fetch_assoc($db_results);
      Dba::finish($db_results);
      if ($row['catalog_id']) {
        Error::add('general','Duplicate Feature ID - ' . $catalog_id);
        $db_results = Dba::write($unlock_sql);
        return false;
      }
    } // else

    // Now it's safe to insert it
    $site = Dba::escape($input['site']);
    $catalog_id = Dba::escape($input['catalog_id']);
    $description = Dba::escape($input['description']);
    $keywords = Dba::escape($input['keywords']);
    $user = Dba::escape(\UI\sess::$user->uid);
    $created = time();
    $sql = "INSERT INTO `feature` (`site`,`catalog_id`,`description`,`keywords`,`user`,`created`) " . 
      "VALUES ('$site','$catalog_id','$description','$keywords','$user','$created')";
    $db_results = Dba::write($sql);

    if (!$db_results) { 
      Error:add('general','Unknown Error - inserting feature into database');
      $db_results = Dba::write($unlock_sql);
      return false;
    }
    $insert_id = Dba::insert_id();

    $db_results = Dba::write($unlock_sql);

    $log_line = "$site,F-$catalog_id,\"" . addslashes($description) . "\",\"$keywords\"," . \UI\sess::$user->username . ",\"" . date('r',$created) . "\"";
    Event::record('ADD-FEATURE',$log_line);

    return $insert_id;

  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if (!strlen($input['description'])) {
      Error::add('description','Required field');
    }

    if (!strlen($input['keywords'])) {
      Error::add('keywords','Required field');
    }

    // If RN then no others
    if (strlen($input['initial_rn']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Error::add('initial_rn','Initial RN and North/East/Elevation can not be specified at the same time');
    }

    // If no RN then all others
    if (strlen($input['initial_rn']) == 0 AND (!strlen($input['easting']) OR !strlen($input['northing']) OR !strlen($input['elevation']))) {
      Error::add('general','Northing, Easting and Elevation are all required if no Initial RN set');
      if (!strlen($input['easting'])) {
        Error::add('easting','Easting Required');
      }
      if (!strlen($input['northing'])) {
        Error::add('northing','Northing Required');
      }
      if (!strlen($input['elevation'])) {
        Error::add('elevation','Elevation Required');
      }
    }

    if (strlen($input['initial_rn']) == 0) {
      if (!Field::validate('northing',$input['northing'])) {
        Error::add('northing','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('easting',$input['easting'])) {
        Error::add('easting','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('elevation',$input['elevation'])) {
        Error::add('easting','Must be numeric and rounded to three decimals');
      }
    }
    else {
      if (!Field::validate('rn',$input['initial_rn'])) {
        Error::add('initial_rn','Must be numeric');
      }
    }

    if (Error::occurred()) { return false; }

    return true; 

  } // validate


} // end class level
?>
