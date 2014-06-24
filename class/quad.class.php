<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class quad { 

	public $uid; 
	public $name; 
  public static $values = array();

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

  /**
   * is_valid
   * returns true/false if the QUAD is a valid quad value
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
    $fhandle = fopen(Config::get('prefix') . '/config/quads.csv','r');
    $quads = fgetcsv($fhandle);
    self::$values = $quads;

  } // _auto_init


} // quad
?>
