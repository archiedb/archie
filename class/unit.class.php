<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class unit { 

  public $uid; 
  public $name; 
  public static $values = array();

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

  /**
   * This is called when the class is included, we want
   * to load up the allowed UNITS from the config/units.csv
   */
  public static function _auto_init() {

    // Read in the units.csv
    $fhandle = fopen(Config::get('prefix') . '/config/units.csv','r');
    $units = fgetcsv($fhandle);
    self::$values = $units;

  } // _auto_init

}
?>
