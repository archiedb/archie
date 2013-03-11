<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Classification extends database_object { 


	public $uid; 
	public $name; 

	/**
	 * Constructor
	 * Takes a UID and pulls info from the database
	 */
	public function __construct($uid='') { 

                if (!is_numeric($uid)) { return false; }

		$row = $this->get_info($uid); 
		
		if (!count($row)) { return false; }

                foreach ($row as $key=>$value) { $this->$key = $value; }

                return true;

	} // constructor

  /**
    * build_cache
    * Build a cache of our objects, save some queries
    */
  public static function build_cache($objects) {
  
    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `classification` WHERE `classification`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('classification',$row['uid'],$row); 
    }

    return true; 
  
  } // build_cache

	/**
	 * refresh
	 * Refresh the object
	 */
	public function refresh() { 

		Classification::remove_from_cache('classification',$this->uid); 
		$this->__construct($this->uid); 

	} // refresh

	public static function get_from_material($material) { 

		$material = Dba::escape($material); 
		$sql = "SELECT `classification` FROM `material_classification` WHERE `material`='$material'"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		
		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = new Classification($row['classification']); 
		} 

		return $results; 

	} // get_from_material 

	// Get all - returns all of the classifications
	public static function get_all() { 

		$sql = "SELECT * FROM `classification`"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('classification',$row['uid'],$row); 
			$results[] = new Classification($row['uid']); 
		} 

		return $results; 

	} // get_all

	// Get the ID from the name
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `classification` WHERE `name` LIKE '$name'"; 

		$db_results = Dba::read($sql); 
		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

} // classification 
