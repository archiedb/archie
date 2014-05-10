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

    if (!Field::validate('rn',$input['rn'])) { 
      Event::error('SpatialData',$intpu['rn'] . ' is not a valid RN');
      $retval = false;
    }

    if (strlen($input['rn']) AND (strlen($input['easting']) OR strlen($input['northing']) OR strlen($input['elevation']))) {
      Event::error('SpatialData',$input['rn'] . ' was specified in addition to easting/northing or elevation');
      $retval = false;
    }

    // Make sure this RN + Record + Type is unique
    $rn = Dba::escape($input['rn']);
    $type = Dba::escape($input['type']);
    $record = Dba::escape($input['record']);
    $sql = "SELECT * FROM `spatial_data` WHERE `station_index`='$rn' AND `record_type`='$type' AND `record`='$record'";
    $db_results = Dba::read($sql);

    $row = Dba::fetch_assoc($db_results); 
    if (count($row)) {
      if ($row['uid']) { 
        Event::error('SpatialData','Attempted to add duplicate record - ' . $input['rn'] . ' -> ' . $input['type'] . ' - ' . $input['record']); 
        $retval = false; 
      }
    }

    return $retval; 

  } // validate

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
