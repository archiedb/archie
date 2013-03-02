<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */


class Material extends database_object { 


	public $uid; 
	public $name; 

	/**
	 * Constructor
	 * Takes a UID and returns a material from the database
	 */
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid); 

		foreach ($row as $key=>$value) { $this->$key = $value; } 

		return true;  

	} // constructor

	/**
	 * build_cache
	 * Build a cache of our objects, save some queries
	 */
	public static function build_cache($objects) { 


	} // build_cache

	/**
	 * refresh
	 * Refreshes the object from the db
	 */
	public function refresh() { 

		Material::remove_from_cache('material',$this->uid); 
		$this->__construct($this->uid); 
	} 

	// Check to see if this material has the specified classification
	public function has_classification($classification) { 

		$material_id = Dba::escape($this->uid); 
		$classification = Dba::escape($classification); 
		$sql = "SELECT * FROM `material_classification` WHERE `material`='$material_id' AND `classification`='$classification'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results);  

		return $row['material']; 	

	} // has_classification

	// Return the materials 
	public static function get_all() { 

		$sql = "SELECT * FROM `material`"; 
		$db_results = Dba::read($sql); 
		//FIXME: Inefficient 
		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = new Material($row['uid']); 
		} 

		return $results; 

	} // get_all

	// ID from name
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `material` WHERE `name` LIKE '$name'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

} // material
