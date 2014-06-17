<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

class quad { 

	public $uid; 
	public $name; 
	public static $values = array('0'=>'','1'=>'NE','2'=>'SE','3'=>'SW','4'=>'NW'); 

	// Constructor
	public function __construct($uid) { 

		$this->uid = intval($uid); 
		$this->name = isset(quad::$values[$uid]) ? quad::$values[$uid] : null;

		return true; 

	} // uid

	// Take the name and return the ID for it
	public static function name_to_id($name) { 

		// This is very expensive
		$key = array_search($name,quad::$values); 
		return $key; 

	} // name_to_id 

} // quad
?>
