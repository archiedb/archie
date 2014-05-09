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
  private $valid_types = array('record','feature','krotovoina');

	// Constructor takes a uid
	public function __construct($record='',$type='') { 

		if (!is_numeric($record) OR !in_array($type,$this->valid_types)) { return false; } 

    $row = $this->get_info($record,$type);
    foreach ($row as $key=>$value) {
      $this->$key = $value;
    }

		return $retval; 

	} // constructor

  /**
   * get_info
   * Redefined here because this one is odd
   */
  public function get_info($record,$type) {

    // Set Index UID
    $uid = $type . '-' . $record;

    if (self::is_cached('spatial_data',$uid)) {
      return self::get_from_cache('spatial_data',$uid);
    }

    $type = Dba::escape($type);
    $record = Dba::escape($record);
    $sql = "SELECT * FROM `spatial_data` WHERE `record`='$record' AND `type`='$type'";
    $db_results = Dba::read($sql);

    $row = Dba::fetch_assoc($db_results);

    // We didn't find anything? 
    if (!isset($row['uid'])) { return false; }

    parent::add_to_cache('spatial_data',$uid,$row);

    return $row; 

  } // get_info

  /**
   * build_cache
   */
  public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    switch ($type) {
      case 'record':
      case 'krotovina':
      case 'feature':
        $table_name = $type;
      break;
    }

    $sql = 'SELECT * FROM `' . $table_name . '` WHERE `uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache($table_name,$row['uid'],$row); 
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
   */
   public static function create($input) { 

    Error::clear();

    if (!SpatialData::validate($input)) { 
      Error::add('general','Invalid Spatial Data fields - please check input');
      return false;
    }


  } // create

  /**
   * validate
   * Validate the incoming data
   */
  public static function validate($input) { 

    if (Error::occurred()) { return false ; }

    return true; 

  } // validate

} // end class spatial_data
?>
