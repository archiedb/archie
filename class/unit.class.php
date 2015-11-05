<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class unit extends sitesetting { 

  public function __construct($name) {

    $this->name = $name;

  } // constructor

  /**
   * is_valid
   * Return if it's valid or not
   */
  public static function is_valid($name) { 

    if (in_array($name,self::$values['units'])) {
      return true;
    }

    return false;

  } // is_valid

  /**
   * get_values
   * Return all of the values
   */
  public static function get_values() { 

    return self::$values['units'];

  } // get_values

  /**
   * This is called when the class is included, we want
   * to load up the allowed UNITS from the config/units.csv
   */
  public static function _auto_init() {

    // Read in the units.csv
    if (isset(\UI\sess::$user)) {
      self::$values['units'] = \UI\sess::$user->site->get_setting('units');
    }
    else { 
      $fhandle = fopen(Config::get('prefix') . '/config/units.csv.dist','r');
      self::$values['units'] = fgetcsv($fhandle);
    }

  } // _auto_init

}
?>
