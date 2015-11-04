<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class quad extends sitesetting { 

  /**
   * This is called when the class is included, we want
   * to load up the allowed UNITS from the config/units.csv
   */
  public static function _auto_init() {

    // Read in the units.csv
    if (isset(\UI\sess::$user)) {
      self::$values = \UI\sess::$user->site->get_setting('quads');
    }
    else {
      $fhandle = fopen(Config::get('prefix') . '/config/quads.csv.dist','r');
      $quads = fgetcsv($fhandle);
      self::$values = $quads;
    }

  } // _auto_init

} // quad
?>
