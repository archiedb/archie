<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

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

  /**
   * name_to_id
   */
  public static function name_to_id($name) { 

    $uid = array_search($name,self::$values); 
    return $uid; 

  } // name_to_id

  /**
   * is_valid
   * Returns true/false if the UNIT is a valid unit value
   */
  public static function is_valid($name) { 

    if (in_array($name,self::$values)) { 
      return true;
    }

    return false;

  } // is_valid

}
?>
