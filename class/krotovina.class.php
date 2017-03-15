<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Krotovina extends database_object { 

	public $uid; 
  public $site; // FK Site
  public $catalog_id; // Per site Unique value visually displayed as F-#
  public $lsg_unit; // Lithostratagraphic Unit
  public $level; // FK of `Level`.`uid`
  public $keywords;
  public $description;
  public $user; // FK User
  public $image; // Primary Image FK
  public $created;
  public $updated;
  public $closed;
  public $record;
  public $closed_date;
  public $closed_user; // FK User

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid) OR !$uid) { return false; } 

		$row = $this->get_info($uid,'krotovina'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

    $this->record = 'K-' . $this->catalog_id;
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

    $sql = 'SELECT * FROM `krotovina` WHERE `krotovina`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('krotovina',$row['uid'],$row); 
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
		parent::remove_from_cache('krotovina',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Display friendly stuff
   */
  public function _display($variable) { 



  } // _display

  /**
   * update
   * Update an existing krotovina, this is only related to the description and keywords
   * this doesn't deal with the location/media information
   */
  public function update($input) { 

    Err::clear();

    if (!Krotovina::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    $uid          = $input['krotovina_id'];
    $description  = empty($input['description']) ? NULL : $input['description'];
    $keywords     = empty($input['keywords']) ? NULL : $input['keywords'];
    $level        = empty($input['level']) ? NULL : $input['level'];
    $lsg_unit     = empty($input['lsg_unit']) ? NULL : $input['lsg_unit'];
    $updated      = time();
    $sql = "UPDATE `krotovina` SET `updated`=?, `keywords`=?, `description`=?, `level`=?, `lsg_unit`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array($updated,$keywords,$description,$level,$lsg_unit,$uid)); 

    if (!$db_results) { 
      Err::add('general','Unable to update Krotovina - please see error log');
      return false;
    }

    $log_json = json_encode(array('Description'=>$description,'Keywords'=>$keywords,'Level'=>$level,'LSGUnit'=>$lsg_unit,'User'=>\UI\sess::$user->username,'Updated'=>date('r',$updated)));
    Event::record('krotovina::update',$log_json);

    $this->refresh();
    $record = $this->record;

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

    if (!Krotovina::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    // Start the transaction
    if (!Dba::begin_transaction()) { 
      Err::add('general','Unable to start DB Transaction, please try again');
      return false; 
    }

    // Check for existing krotovina
    if (!isset($input['catalog_id'])) {
      $catalog_sql = "SELECT `catalog_id` FROM `krotovina` WHERE `site`=? ORDER BY `catalog_id` DESC LIMIT 1 FOR UPDATE";
      $db_results = Dba::read($catalog_sql,array($input['site']));
      $row = Dba::fetch_assoc($db_results);
      $input['catalog_id'] = $row['catalog_id']+1;
    }
    else { 
      $catalog_sql = "SELECT `catalog_id` FROM `krotovina` WHERE `site`=? AND `catalog_id`=? LIMIT 1 FOR UPDATE";
      $db_results = Dba::read($catalog_sql,array($input['site'],$input['catalog_id']));
      $row = Dba::fetch_assoc($db_results);
      if ($row['catalog_id']) {
        Err::add('general','Duplicate Feature ID - ' . $catalog_id);
        Dba::commit();
        return false;
      }
    } // else

    //FIXME: Change to NULL once DB change is in place update_0019()
    // Now it's safe to insert it
    $created  = time();
    $level    = strlen($input['level']) ? $input['level'] : NULL;
    $lsg_unit = strlen($input['lsg_unit']) ? $input['lsg_unit'] : NULL;

    $sql = "INSERT INTO `krotovina` (`site`,`catalog_id`,`description`,`keywords`,`level`,`lsg_unit`,`user`,`created`) VALUES (?,?,?,?,?,?,?,?)";
    $db_results = Dba::write($sql,array($input['site'],$input['catalog_id'],$input['description'],$input['keywords'],$level,$lsg_unit,\UI\sess::$user->uid,$created));

    if (!$db_results) { 
      Error:add('general','Unknown Error - inserting krotovina into database');
      $retval = Dba::rollback();
      if (!$retval) { Err::add('general','Unable to roll database changes back, please report this to your Administrator'); }
      Dba::commit();
      return false;
    }

    // Take the insert_id and return it
    $insert_id = Dba::insert_id();
    
    $log_json = json_encode(array('Site'=>$input['site'],'Catalog ID'=>$input['catalog_id'],'Description'=>$input['description'],
        'Keywords'=>$input['keywords'],'Level'=>$level,'LSG Unit'=>$lsg_unit,'User'=>\UI\sess::$user->username,'Created'=>date("r",$created)));

    Event::record('krotovina::create',$log_json);
    
    // Now we add the initial spatial data
    $spatialdata = SpatialData::create(array('record'=>$insert_id,'type'=>'krotovina','station_index'=>$input['station_index'],'northing'=>$input['northing'],
                      'easting'=>$input['easting'],'elevation'=>$input['elevation']));

    if (!$spatialdata) { 
      Err::add('general','Unable to insert Spatial Information, but Krotovina created, please manually add point');
    }

    if (!Dba::commit()) { 
      Event::record('DBA::commit','Commit Failure - unable to close transaction');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if (!strlen($input['description'])) {
      Err::add('description','Required field');
    }

    if (!strlen($input['keywords'])) {
      Err::add('keywords','Required field');
    }

    // If RN then no others
    if (strlen($input['station_index']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Err::add('station_index','Initial RN and North/East/Elevation can not be specified at the same time');
      if (!Field::validate('station_index',$input['station_index'])) {
        Err::add('station_index','Must be numeric');
      }
    }
    // If no RN then all others - unless we have a krotovina_id
    if (!$input['krotovina_id'] AND strlen($input['station_index']) == 0 AND (!strlen($input['easting']) OR !strlen($input['northing']) OR !strlen($input['elevation']))) {
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
    } // End if No RN

    // Make sure the RN isn't duplicated for this site. 
    $input['rn'] = $input['station_index'];
    if (!SpatialData::is_site_unique($input,$input['krotovina_id'])) {
      Err::add('station_index','Duplicate RN in this site');
    }

    // If they specified a level, it must be valid
    if (strlen($input['level'])) {
      $level = new Level($input['level']);
      if (!$level->catalog_id) {
        Err::add('level','Level not found, please create level record first');
      }
    }

    // Make sure the LSG unit is valid
    if (!Lsgunit::is_valid($input['lsg_unit'])) {
      Err::add('lsg_unit','Invalid Lithostratigraphic Unit');
    }

    if (Err::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * add_point
   * Add a krotovina spatial_data point
   */
  public function add_point($input) { 

    Err::clear();

    $station_index  = empty($input['station_index']) ? NULL : $input['station_index'];
    $northing       = empty($input['northing']) ? NULL : $input['northing'];
    $easting        = empty($input['easting']) ? NULL : $input['easting'];
    $elevation      = empty($input['elevation']) ? NULL : $input['elevation'];
    $note           = empty($input['note']) ? NULL : $input['note'];

    // Really we're just going to be using the spatialdata class for this, but
    // we want to set the data and type correctly so here we are
    $retval = SpatialData::create(array(
      'record'=>$this->uid,
      'type'=>'krotovina',
      'station_index'=>$station_index,
      'northing'=>$northing,
      'easting'=>$easting,
      'elevation'=>$elevation,
      'note'=>$note));

    return $retval;

  } // add_point

  /**
   * update_point
   * Update existing point
   */
  public function update_point($input) { 

    Err::clear();

    $point = new SpatialData($input['spatialdata_id']);

    $retval = $point->update(array('spatialdata_id'=>$point->uid,
      'record'=>$this->uid,
      'type'=>'krotovina',
      'station_index'=>$input['station_index'],
      'northing'=>$input['northing'],
      'easting'=>$input['easting'],
      'elevation'=>$input['elevation'],
      'note'=>$input['note']));


    return $retval; 

  } // update_point

  /*
   * del_point
   * Remove a point from the krotovina record
   */
  public function del_point($uid) { 

    $retval = SpatialData::remove($uid);

    return $retval;

  } // del_point

  /**
   * get_uid_from_record
   * Return the UID from the record entry
   */
  public static function get_uid_from_record($catalog_id,$site='') {

    if (!$site) { 
      $site = \UI\sess::$user->site->uid;
    }

    $catalog_id = Dba::escape($catalog_id);
    $site = Dba::escape($site);

    $sql = "SELECT * FROM `krotovina` WHERE `catalog_id`='$catalog_id' AND `site`='$site'";
    $db_results = Dba::read($sql);

    $row = Dba::fetch_assoc($db_results);

    if (!isset($row['uid'])) { return false; }

    // Cache it!
    parent::add_to_cache('krotovina',$row['uid'],$row);

    return $row['uid'];

  } // get_uid_from_record

  /**
   * delete
   * Delete the krotovina record
   */
  public function delete () { 

    // remove the spatial data
    if (!SpatialData::delete_by_record($this->uid,'krotovina')) {
      Event::error('Krotovina','Unable to delete Spatial data [ ' . $this->uid . ' ] aborting krotovina delete');
      return false; 
    }

    $uid = Dba::escape($this->uid);
    $sql = "DELETE FROM `krotovina` WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return true;

  } // delete

  /**
   * has_records
   * Returns true if there are records for this krotovina
   */
  public function has_records() { 

    $uid = Dba::escape($this->uid);

    $sql = "SELECT COUNT(`uid`) AS `count` FROM `record` WHERE `krotovina`='$uid'";
    $db_results = Dba::read($sql);

    $results = Dba::fetch_assoc($db_results);

    if ($results['count'] > 0) { return true; }

    return false;

  } // has_records

  /**
   * get_user_krotovina
   * Returns the krotovina assoicated with this user
  */
  public static function get_user_krotovina($uid=false,$limit=3) { 

    if (!$uid) {
      $uid = \UI\sess::$user->uid;
    }

    $results = array();

    $uid = Dba::escape($uid);
    $limit = abs(floor($limit));
    $sql = "SELECT * FROM `krotovina` WHERE `user`='$uid' AND `site`=? ORDER BY `created` DESC LIMIT $limit";
    $db_results = Dba::read($sql,array(\UI\sess::$user->site->uid));

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid'];
      parent::add_to_cache('krotovina',$row['uid'],$row);
    }

    return $results;

  } // get_user_krotovina

} // end class level
?>
