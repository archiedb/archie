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
    foreach ($row as $key=>$value) {
      $this->$key = $value;
    }

		return $retval; 

	} // constructor


  /**
   * build_cache
   */
  public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `spatial_data` WHERE `uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('spatial_data',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
    parent::remove_from_cache($this->type,$this->record);
		$this->__construct($this->record,$this->type); 

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

    if (!SpatialData::validate($input)) { 
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }

    $record = Dba::escape($input['record']);
    $type = Dba::escape($input['type']);
    $station_index = Dba::escape($input['rn']); 
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $elevation = Dba::escape($input['elevation']);
    $note = Dba::escape($input['note']);
    $sql = "INSERT INTO `spatial_data` (`record`,`record_type`,`station_index`,`northing`,`easting`,`elevation`,`note`) " . 
        "VALUES ('$record','$type','$station_index','$northing','$easting','$elevation','$note')";
    $db_results = Dba::write($sql); 

    $insert_id = Dba::insert_id();

    if (!$insert_id) { 
      Error:add('general','Database Error - Please contact your administrator');
      return false;
    }

    return $insert_id;

  } // create

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
      if (isset($row['uid'])) { 
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
      if (isset($row['uid'])) {
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
  public static function get_record_data($record,$type) {

    $results = array();
    if (!is_numeric($record) or !in_array($type,SpatialData::$valid_types)) { return array(); }

    $type = Dba::escape($type);
    $record = Dba::escape($record); 
    $sql = "SELECT * FROM `spatial_data` WHERE `record_type`='$type' AND `record`='$record' ORDER BY `station_index` DESC";
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('spatial_data',$row['uid'],$row);
      $results[] = $row;
    }

    return $results;

  } // get_record_data

} // end class spatial_data
?>
