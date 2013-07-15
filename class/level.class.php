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
		// Don't actually keep this in the object 
		unset($this->password); 

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
