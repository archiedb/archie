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

    $row = $this->get_info($uid,'spatial_data');
    //FIXME: DB needs to allow null station_index
    if (!count($row)) { return true; }
    if ($row['station_index'] == '0') { $row['station_index'] = ''; }
    foreach ($row as $key=>$value) {
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

    $uid = Dba::escape($uid);
    $sql = "DELETE FROM `spatial_data` WHERE `uid`='$uid' LIMIT 1";
    $db_results = Dba::write($sql); 

    Event::record('SpatialData','Deleted Spatial Data UID:' . $uid . ' RN:' . $spatialdata->station_index . ' North:' . $spatialdata->northing . ' East:' . $spatialdata->easting . ' Elev:' . $spatialdata->elevation . ' Note:' . $spatialdata->note);

    return $db_results;

  } // remove

  /**
   * create
   * Enter in a new record
   * Input array should be array('record','type','rn','northing','easting','elevation','note');
   */
   public static function create($input) { 

    if (!isset($input['rn']) AND isset($input['station_index'])) {
      $input['rn'] = $input['station_index'];
    }

    if (!SpatialData::validate($input)) { 
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }

    $record = Dba::escape($input['record']);
    $type = Dba::escape($input['type']);
//FIXME: NULL not allowed yet
//    $station_index = $input['rn'] != 0 ? "'".Dba::escape($input['rn'])."'" : 'NULL';
    $station_index = $input['rn'] != 0 ? "'".Dba::escape($input['rn'])."'" : "'0'";
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $elevation = Dba::escape($input['elevation']);
    $note = Dba::escape($input['note']);
    $sql = "INSERT INTO `spatial_data` (`record`,`record_type`,`station_index`,`northing`,`easting`,`elevation`,`note`) " . 
        "VALUES ('$record','$type',$station_index,'$northing','$easting','$elevation','$note')";
    $db_results = Dba::write($sql); 

    $insert_id = Dba::insert_id();

    if (!$insert_id) { 
      Error:add('general','Database Error inserting Spatial Data - Please contact your administrator');
      Event::error('SpatialData',"Error inserting spatial data - UID:$record  --- Type:$type --- Station index:$station_index --- Nor:$northing --- Est:$easting --- Elv:$elevation --- Note:$note");
      return false;
    }

    Event::add('SpatialData',"Added point for UID:$record of type $type RN:$station_index,Nor:$northing,Est:$easting,Elv:$elevation");
    return $insert_id;

  } // create

  /**
   * update
   * Update an existing spatial record
   */
  public function update($input) {

    $input['type'] = $this->record_type;
    $input['record'] = $this->record;
    $input['update'] = true; 

//FIXME: BAD BAD BAD BAD BAD
    if (!SpatialData::validate($input)) {
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }

    // Escape the values
    $uid = Dba::escape($this->uid);
    $type = Dba::escape($this->record_type);
    $record = Dba::escape($this->record);
//    $station_index = $input['rn'] != 0 ? "'".Dba::escape($input['rn'])."'" : 'NULL';
//FIXME: This is broken
    $station_index = $input['rn'] != 0 ? "'".Dba::escape($input['rn'])."'" : "'0'";
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $elevation = Dba::escape($input['elevation']);

    $sql = "UPDATE `spatial_data` SET `station_index`=$station_index,`northing`='$northing',`easting`='$easting',`elevation`='$elevation' " . 
      "WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    if (!$db_results) { 
      return false;
    }

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

    // If specified
    if (strlen($input['rn'])) { 
      if (!Field::validate('rn',$input['rn'])) { 
        Event::error('SpatialData',$input['rn'] . ' is not a valid RN');
        Error::add('Total Station Index','Invalid RN specified');
        $retval = false;
      }
    }

    // 0.000 doesn't count
    $input['easting'] = $input['easting'] == '0.000' ? '' : $input['easting'];
    $input['northing'] = $input['northing'] == '0.000' ? '' : $input['northing'];
    $input['elevation'] = $input['elevation'] == '0.000' ? '' : $input['elevation'];

    if (strlen($input['rn']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Event::error('SpatialData',$input['rn'] . ' was specified in addition to easting/northing or elevation');
      Error::add('Total Station Index','RN and Elevation/Northing/Easting specified, only one may be set');
      $retval = false;
    }

    if (strlen($input['rn'])) {
      // Make sure this RN + Record + Type is unique
      $rn = Dba::escape($input['rn']);
      $type = Dba::escape($input['type']);
      $record = Dba::escape($input['record']);
      $sql = "SELECT * FROM `spatial_data` WHERE `station_index`='$rn' AND `record_type`='$type' AND `record`='$record'";
      $db_results = Dba::read($sql);

      $row = Dba::fetch_assoc($db_results); 
      if (isset($row['uid']) AND !$input['update']) { 
        Event::error('SpatialData','Attempted to add duplicate record - ' . $input['rn'] . ' -> ' . $input['type'] . ' - ' . $input['record']); 
        Error::add('Total Station Index','RN Already assoicated with this record');
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
        Event::error('SpatialData','Attempted to add duplicate record - ' . $input['northing'] . ' / ' . $input['easting'], ' / ' . $input['elevation'] . ' -> ' . $input['type'] . ' / ' . $input['record']);
        Error::add('Northing / Easting / Elevation','Point already assoicated with this record');
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

    return $retval; 

  } // validate

  /**
   * is_site_unique
   * Return true if the point is unique for the specified site
   */
  public static function is_site_unique($input,$record=0) { 

    $input['rn'] = isset($input['rn']) ? $input['rn'] : '';

    $input['northing'] = isset($input['northing']) ? $input['northing'] : '';
    $input['easting'] = isset($input['easting']) ? $input['easting'] : '';
    $input['elevation'] = isset($input['elevation']) ? $input['elevation'] : '';

    $query = array();

    if (strlen($input['northing']) AND strlen($input['easting']) AND strlen($input['elevation'])) { 
      $cord_sql = "(`northing`=? AND `easting`=? AND `elevation`=?) OR";
      $query[] = $input['northing'];
      $query[] = $input['easting'];
      $query[] = $input['elevation'];
    }
    if (strlen($input['rn'])) { 
      $rn_sql = "`station_index`=?";
      $query[] = $input['rn'];
    }


    $sql = "SELECT * FROM `spatial_data` WHERE $cord_sql $rn_sql";
    $sql = rtrim($sql,'OR');
    $db_results = Dba::read($sql,$query);

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
