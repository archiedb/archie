<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

class lsgunit { 

	public $uid; 
	public $name; 
	public static $values = array('1'=>'','2'=>'Fill','3'=>'1','4'=>'2','5'=>'3','6'=>'4','7'=>'5','8'=>'6','9'=>'7','10'=>'8','11'=>'Other'); 

	// Constructor
	public function __construct($uid) { 
		$this->uid = intval($uid); 
		$this->name = lsgunit::$values[$uid]; 
		
		return true; 

	} // uid

	// Get the ID from the name
	public static function name_to_id($name) { 

		$uid = array_search($name,lsgunit::$values); 
		return $uid; 

	} // name_to_id

}
?>
