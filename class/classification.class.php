<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */


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

                foreach ($row as $key=>$value) { $this->$key = $value; }

                return true;

	} // constructor

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
