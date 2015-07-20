<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class SpatialData extends database_object { 

	public $uid; 
  public $record; // FK Record UID
  public $record_type; // Source of Spatial data
  public $station_index; // RN
  public $northing; // RAW value
  public $easting; // RAW value
  public $elevation; // RAW value
  public $note; // Allowed by total station so why not!

  // Valid types
  private static $valid_types = array('record','feature','krotovina');

	// Constructor takes a uid
	public function __construct($uid='') { 

    if (!$uid OR !is_numeric($uid)) { return true; }

    $row = $this->get_info($uid,'spatial_data');
    if (!is_array($row)) { return true; }
    foreach ($row as $key=>$value) {
      if ($value === 0) { $value = '';}
      $this->$key = $value;
    }

		return true; 

	} // constructor


  /**
   * build_cache
   */
  public static function build_cache($objects,$type) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    // Build empty array so that we cache negative responses
    foreach ($objects as $uid) {
      $results[$uid] = array();
    }

    $type = Dba::escape($type);
    $sql = "SELECT * FROM `spatial_data` WHERE `record_type`='$type' AND `record` IN " . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[$row['record']][] = $row;
    }

    foreach ($results as $record=>$row) {
      $cacheid = $type . '_' . $record;
      parent::add_to_cache('spatial_data',$cacheid,$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

    // Doesn't make sense for this type of record

	} // refresh

  /**
   * remove
   * Delete a record
   * Input should be uid
   */
  public static function remove($uid) {

    // Load it we need to track this
    $spatialdata = new SpatialData($uid);

    $sql = "DELETE FROM `spatial_data` WHERE `uid`=? LIMIT 1";
    $db_results = Dba::write($sql,array($uid)); 

    $json_msg = json_encode(array('uid'=>$uid,'station_index'=>$spatialdata->station_index,'nor'=>$spatialdata->northing,'est'=>$spatialdata->easting,'elv'=>$spatialdata->elevation,'note'=>$spatialdata->note));

    Event::record('spatialdata::remove','Deleted Spatial Data -- ' . $json_msg);

    return $db_results;

  } // remove

  /**
   * create
   * Enter in a new record
   * Input array should be array('record','type','station_index','northing','easting','elevation','note');
   */
   public static function create($input) { 

    if (!SpatialData::validate($input)) { 
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }

    $record         = $input['record'];
    $type           = $input['type'];
    $station_index  = isset($input['station_index']) ? $input['station_index'] : NULL;
    $northing       = isset($input['northing']) ? $input['northing'] : NULL;
    $easting        = isset($input['easting']) ? $input['easting'] : NULL;
    $elevation      = isset($input['elevation']) ? $input['elevation'] : NULL;
    $note           = isset($input['note']) ? $input['note'] : NULL;
    
    $sql = "INSERT INTO `spatial_data` (`record`,`record_type`,`station_index`,`northing`,`easting`,`elevation`,`note`) " . 
        "VALUES (?,?,?,?,?,?,?)";
    $db_results = Dba::write($sql,array($record,$type,$station_index,$northing,$easting,$elevation,$note)); 

    $insert_id = Dba::insert_id();

    if (!$insert_id) { 
      Error::add('general','Unable to insert Spatial Data - Please contact your administrator');
      Event::error('SpatialData::create',"Error inserting spatial data - UID:$record  --- Type:$type --- Station index:$station_index --- Nor:$northing --- Est:$easting --- Elv:$elevation --- Note:$note");
      return false;
    }

    $json_msg = json_encode(array('uid'=>$insert_id,'record'=>$record,'type'=>$type,
      'station_index'=>$station_index,'nor'=>$northing,'est'=>$easting,'elv'=>$elevation,'note'=>$note));

    Event::add('SpatialData::create',$json_msg);
    return $insert_id;

  } // create

  /**
   * update
   * Update an existing spatial record
   */
  public function update($input) {

    $input['type'] = $this->record_type;
    $input['record'] = $this->record;
//FIXME: BAD BAD BAD BAD BAD
    $input['update'] = true; 

    if (!SpatialData::validate($input)) {
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }

    // Escape the values, and set to null if they aren't set
    $uid            = $this->uid;
    $type           = $this->record_type;
    $record         = $this->record;
    $station_index  = isset($input['station_index']) ? $input['station_index'] : NULL;
    $northing       = isset($input['northing']) ? $input['northing'] : NULL;
    $easting        = isset($input['easting']) ? $input['easting'] : NULL;
    $elevation      = isset($input['elevation']) ? $input['elevation'] : NULL;
    $note           = isset($input['note']) ? $input['note'] : NULL;

    $sql = "UPDATE `spatial_data` SET `station_index`=?,`northing`=?,`easting`=?,`elevation`=?,`note`=? WHERE `uid`=? "; 
    $db_results = Dba::write($sql,array($station_index,$northing,$easting,$elevation,$note,$uid));

    if (!$db_results) { 
      return false;
    }

    $json_msg = json_encode(array('uid'=>$insert_id,'record'=>$record,'type'=>$type,
      'station_index'=>$station_index,'nor'=>$northing,'est'=>$easting,'elv'=>$elevation,'note'=>$note));

    Event::add('SpatialData::update',$json_msg);

    return true;

  } // update

  /**
   * validate
   * Validate the incoming data
   */
  public static function validate($input) { 

    // This is a little different, this isn't a 
    // forward facing object, so it doesn't add errors, but
    // just fails
    $retval = true;

    // They have to enter something
    if (!strlen($input['station_index']) AND !strlen($input['northing']) AND !strlen($input['easting']) AND !strlen($input['elevation'])) {
      Error::add('station_index','Must specify Station Index (RN) or Northing, Easting & Elevation');
      $retval = false;
    }

    // If specified
    if (strlen($input['station_index'])) { 
      if (!Field::validate('station_index',$input['station_index'])) { 
        Event::error('SpatialData',$input['station_index'] . ' is not a valid Station Index (RN)');
        Error::add('Total Station Index','Invalid Station Index (RN) specified');
        $retval = false;
      }
    }

    // If station_index AND elv,est,nor are specified
    if (strlen($input['station_index']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      // If this is an "update" then there's a chance they are working with imported data
      // If so make sure they only changed the station_index or note
      if (isset($input['update'])) { 
        $point = new SpatialData($input['spatialdata_id']);
        if ($point->northing != $input['northing'] OR $point->easting != $input['easting'] OR $point->elevation != $input['elevation'] AND strlen($input['station_index'])) {
          Error::add('Total Station Index','Cannot update cordinates if Station Index is specified');
          $retval = false; 
        }

      } // 
      else { 
        Event::error('SpatialData','Station Index' . $input['station_index'] . ' was specified in addition to easting/northing or elevation');
        Error::add('Total Station Index','Station Index (RN) and Elevation/Northing/Easting specified, only one may be set');
        $retval = false;
      }
    }

    if (strlen($input['station_index'])) {
      // Make sure this RN + Record + Type is unique
      $station_index = Dba::escape($input['station_index']);
      $type = Dba::escape($input['type']);
      $record = Dba::escape($input['record']);
      $sql = "SELECT * FROM `spatial_data` WHERE `station_index`='$station_index' AND `record_type`='$type' AND `record`='$record'";
      $db_results = Dba::read($sql);

      $row = Dba::fetch_assoc($db_results); 
      if (isset($row['uid']) AND !$input['update']) { 
        Event::error('SpatialData::validate','Attempted to add duplicate record - ' . $input['station_index'] . ' -> ' . $input['type'] . ' - ' . $input['record']); 
        Error::add('Total Station Index','Station Index (RN) already exists cannot create duplicate record');
        $retval = false; 
      }
    } // end if RN
    else { 
      // Make sure that the northing/easting/elevation are unique
      $northing = Dba::escape($input['northing']); 
      $easting = Dba::escape($input['easting']); 
      $elevation = Dba::escape($input['elevation']);
      $type = Dba::escape($input['type']);
      $record = Dba::escape($input['record']);
      $sql = "SELECT * FROM `spatial_data` WHERE `northing`='$northing' AND `easting`='$easting' AND `elevation`='$elevation' AND `record_type`='$type' AND `record`='$record'";
      $db_results = Dba::read($sql); 

      $row = Dba::fetch_assoc($db_results);
      if (isset($row['uid']) AND !$input['update']) {
        Event::error('SpatialData::validate','Attempted to add duplicate record - ' . $input['northing'] . ' / ' . $input['easting'], ' / ' . $input['elevation'] . ' -> ' . $input['type'] . ' / ' . $input['record']);
        Error::add('Northing / Easting / Elevation','Point already exists cannot create duplicate record');
        $retval = false;
      }

      // Make sure northing/easting/elevation are valid
      if (!Field::validate('northing',$input['northing'])) {
        Error::add('Northing','Invalid Value, must be numeric and rounded to 3 places');
        $retval = false;
      }
      if (!Field::validate('easting',$input['easting'])) {
        Error::add('Easting','Invalid Value, must be numeric and rounded to 3 places');
        $retval = false;
      }
      if (!Field::validate('elevation',$input['elevation'])) {
        Error::add('Elevation','Invalid Value, must be numeric and rounded to 3 places');
        $retval = false;
      }
        
    } // if northing/easting/elevation

    // Make sure it's unique to the site
    if (!SpatialData::is_site_unique($input,$input['record'])) {
      Error::add('Spatialdata','Station Index (RN) / Northing / Easting / Elevation already exists cannot create duplicate record');
      $retval = false;
    }

    return $retval; 

  } // validate

  /**
   * is_site_unique
   * Return true if the point is unique for the specified site
   */
  public static function is_site_unique($input,$record=0) { 

    $input['station_index'] = isset($input['station_index']) ? $input['station_index'] : '';
    $input['northing']      = isset($input['northing']) ? $input['northing'] : '';
    $input['easting']       = isset($input['easting']) ? $input['easting'] : '';
    $input['elevation']     = isset($input['elevation']) ? $input['elevation'] : '';

    // If they've passed nothing then yes it's unique
    if (!strlen($input['northing']) AND !strlen($input['easting']) AND !strlen($input['elevation']) AND !strlen($input['station_index'])) {
      return true;
    }

    $query = array();
    $station_index_sql = '';
    $cord_sql = '';

    if (strlen($input['northing']) AND strlen($input['easting']) AND strlen($input['elevation'])) { 
      $cord_sql = "(`northing`=? AND `easting`=? AND `elevation`=?) OR";
      $query[] = $input['northing'];
      $query[] = $input['easting'];
      $query[] = $input['elevation'];
    }
    if (strlen($input['station_index'])) { 
      $station_index_sql = "`station_index`=?";
      $query[] = $input['station_index'];
    }

    $sql = "SELECT * FROM `spatial_data` WHERE $cord_sql $station_index_sql";
    $sql = rtrim($sql,'OR ');
    $db_results = Dba::read($sql,$query);

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row;
    }
    // If we haven't found a row then s'all good
    if (count($results) == 0) { return true; }

    // If we found something we need to see if it's the same time
    foreach ($results as $row) { 

      switch ($row['record_type']) {
        case 'record':
          $sql = "SELECT * FROM `record` WHERE `uid`=? AND `site`=?";
        break;
        case 'feature':
          $sql = "SELECT * FROM `feature` WHERE `uid`=? AND `site`=?";
        break;
        case 'krotovina':
          $sql = "SELECT * FROM `krotovina` WHERE `uid`=? AND `site`=?";
        break;
        case 'level':
          $sql = "SELECT * FROM `level` WHERE `uid`=? AND `site`=?";
        break;
      }

      $db_results = Dba::read($sql,array($row['record'],\UI\sess::$user->site->uid));
      $row = Dba::fetch_assoc($db_results);

      if (isset($row['uid']) AND $row['uid'] != $record) { return false; }

    } 
    
    return true;

  } // is_site_unqiue

  /**
   * delete_by_record
   * Delete all spatial points related to the specified record
   */
  public static function delete_by_record($record,$type) { 

    if (!is_numeric($record) or !in_array($type,SpatialData::$valid_types)) { return false; }

    $record = Dba::escape($record);
    $type = Dba::escape($type);

    $sql = "DELETE FROM `spatial_data` WHERE `record_type`='$type' AND `record`='$record'";
    $db_results = Dba::write($sql); 

    return true; 

  } // delete_by_record

  /**
   * get_record_data
   * Returns an array of the data for a specific record
   */
  public static function get_record_data($record,$type,$single=false) {

    $results = array();
    if (!is_numeric($record) or !in_array($type,SpatialData::$valid_types)) { return array(); }

    // See if it's cached
    $cacheid = $type . '_' . $record;
    if (parent::is_cached('spatial_data',$cacheid)) {
      $row = parent::get_from_cache('spatial_data',$cacheid);
      if ($single AND count($row)) { return new SpatialData($row['0']['uid']); }
      return $row;
    }

    $sql = "SELECT * FROM `spatial_data` WHERE `record_type`=? AND `record`=? ORDER BY `station_index` DESC";
    $db_results = Dba::read($sql,array($type,$record)); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      if ($single) { return new SpatialData($row['uid']); }
      $results[] = $row;
    }
    
    // If nothing found return empty
    if ($single AND !count($results)) { return new SpatialData(0); }
    
    parent::add_to_cache('spatial_data',$cacheid,$results);

    return $results;

  } // get_record_data

} // end class spatial_data
?>
