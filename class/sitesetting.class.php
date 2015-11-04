<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

abstract class sitesetting { 

	public $name; 
  public static $values = array();

  // Individual _auto_init functions
  abstract public static function _auto_init();

	// Constructor
	public function __construct($name) { 

    if (self::is_valid($name)) {
      $this->name = $name;
      return true;
    }

    return false;

	} // uid

  /**
   * _print
   * Output the value
   */
  public function _print($key) { 

    if (!isset($this->$key)) { return false; }

    echo scrub_out($this->$key);

  } // _print

  /**
   * is_valid
   * returns true/false if the name is a valid value
   */
  public static function is_valid($name) { 

    if (in_array($name,self::$values)) { 
      return true;
    }

    return false; 

  } // is_valid
	
} // sitesetting
?>
