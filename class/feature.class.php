<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Feature extends database_object { 

	public $uid; 
  public $site; // FK Site
  public $catalog_id; // Per site Unique value visually displayed as F-#
  public $keywords;
  public $description;
  public $user; // FK User
  public $created;
  public $updated;
  public $closed;
  public $closed_date;
  public $record;
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
   * update
   * Update an existing feature, this is only related to the description and keywords
   * this doesn't deal with the location/media information
   */
  public function update($input) { 

    Error::clear();

    if (!Feature::validate($input)) {
      Error::add('general','Invalid Field Values - please check input');
      return false;
    }

    $uid = Dba::escape($input['feature_id']);
    $description = Dba::escape($input['description']);
    $keywords = Dba::escape($input['keywords']);
    $updated = time();
    $sql = "UPDATE `feature` SET `updated`='$updated', `keywords`='$keywords', `description`='$description' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    $this->refresh();
    $record = $this->record;
    $log_line = "$site,$record,\"" . addslashes($description) . "\",\"$keywords\"," . \UI\sess::$user->username . ",\"" . date('r',$updated) . "\"";
    Event::record('UPDATE-FEATURE',$log_line);

    return $db_results;

  } // update

  /**
   * create
   * Create a new Feature, this also has to insert the initial spatial location
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
    $lock_sql = "LOCK TABLES `feature` WRITE";
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

    if (!isset($input['catalog_id'])) {
      $site = Dba::escape($input['site']);
      $catalog_sql = "SELECT `catalog_id` FROM `feature` WHERE `site`='$site' ORDER BY `catalog_id` DESC LIMIT 1";
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
    
    // Now we add the initial spatial data
    $spatialdata = SpatialData::create(array('record'=>$insert_id,'type'=>'feature','rn'=>$input['initial_rn'],'northing'=>$input['northing'],
                      'easting'=>$input['easting'],'elevation'=>$input['elevation']));

    if (!$spatialdata) { 
      Error::add('general','Error inserting Spatial Information - please contact your administrator');
    }


    $log_line = "$site,F-$catalog_id,\"" . addslashes($description) . "\",\"$keywords\"," . \UI\sess::$user->username . ",\"" . date('r',$created) . "\"";
    Event::record('ADD-FEATURE',$log_line);

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

    if (!strlen($input['description'])) {
      Error::add('description','Required field');
    }

    // If RN then no others
    if (strlen($input['initial_rn']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Error::add('initial_rn','Initial RN and North/East/Elevation can not be specified at the same time');
      if (!Field::validate('rn',$input['initial_rn'])) {
        Error::add('initial_rn','Must be numeric');
      }

    }
    // If no RN then all others - unless we have a feature_id
    if (!$input['feature_id'] AND strlen($input['initial_rn']) == 0 AND (!strlen($input['easting']) OR !strlen($input['northing']) OR !strlen($input['elevation']))) {
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
      if (!Field::validate('northing',$input['northing'])) {
        Error::add('northing','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('easting',$input['easting'])) {
        Error::add('easting','Must be numeric and rounded to three decimals');
      }
      if (!Field::validate('elevation',$input['elevation'])) {
        Error::add('easting','Must be numeric and rounded to three decimals');
      }
    } // if No RUN specified

    // Make sure the RN isn't duplicated for this site. 
    $input['rn'] = $input['initial_rn'];
    if (!SpatialData::is_site_unique($input,$input['feature_id'])) {
      Error::add('initial_rn','Duplicate RN in this site');
    }

    if (Error::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * add_point
   * Add a feature spatial_data point
   */
  public function add_point($input) { 

    Error::clear(); 

    $retval = SpatialData::create(array(
      'record'=>$this->uid,
      'type'=>'feature',
      'rn'=>$input['rn'],
      'northing'=>$input['northing'],
      'easting'=>$input['easting'],
      'elevation'=>$input['elevation'],
      'note'=>$input['note']));

    return $retval;

  } //add_point

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

    // Return true false if there are records for this feature
    $uid = Dba::escape($this->uid);

    $sql = "SELECT COUNT(`uid`) AS `count` FROM `record` WHERE `feature`='$uid'";
    $db_results = Dba::read($sql);

    $results = Dba::fetch_assoc($db_results);

    if ($results['count'] > 0) { 
      return true; 
    }

    return false; 

  } // has_records

} // end feature level
?>
