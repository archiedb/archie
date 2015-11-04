<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class quad extends sitesetting { 

  public function __construct($name) { 

    $this->name = $name;

  }

  /**
   * is_valid
   */
  public static function is_valid($name) { 

    if (in_array($name,self::$values['quads'])) {
      return true; 
    }

    return false;

  } // is_valid

  /**
   * get_values
   * Return all possible values
   */
  public static function get_values() {
  
    return self::$values['quads'];

  } // get_values

  /**
   * This is called when the class is included, we want
   * to load up the allowed UNITS from the config/units.csv
   */
  public static function _auto_init() {

    // Read in the units.csv
    if (isset(\UI\sess::$user)) {
      self::$values['quads'] = \UI\sess::$user->site->get_setting('quads');
    }
    else {
      $fhandle = fopen(Config::get('prefix') . '/config/quads.csv.dist','r');
      self::$values['quads'] = fgetcsv($fhandle);
    }

  } // _auto_init

} // quad
?>
