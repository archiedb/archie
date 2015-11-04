<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class lsgunit extends sitesetting { 

  public function __construct($name) { 

    $this->name = $name;

  }

  /**
   * is_valid
   * Returns if the name is valid
   */
  public static function is_valid($name) { 

    if (in_array($name,self::$values['lus'])) {
      return true;
    }

    return false;

  } // is_valid

  /**
   * get_values
   * Return all of the values for this
   */
  public static function get_values() {

    return self::$values['lus'];

  } // get_values

  /**
   * This is called when the class is included, we want to load up the
   * allowed LUs from the users site
   */
  public static function _auto_init() { 

    if (isset(\UI\sess::$user)) {
      self::$values['lus'] = \UI\sess::$user->site->get_setting('lus');
    }
    if (empty(self::$values['lus'])) {
      $fhandle = fopen(Config::get('prefix') . '/config/lus.csv.dist','r');
      self::$values['lus'] = fgetcsv($fhandle);
    }

  } // _auto_init

}
?>
