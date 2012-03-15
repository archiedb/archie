<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

class unit { 

	public $uid; 
	public $name; 
	public static $values = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'); 

	// Constructor
	public function __construct($uid) { 

		$this->uid = $uid; 
		$this->name = $uid; 

		return true; 

	} // uid

}
?>
