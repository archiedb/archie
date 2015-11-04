<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class lsgunit extends sitesetting { 

  /**
   * This is called when the class is included, we want to load up the
   * allowed LUs from the users site
   */
  public static function _auto_init() { 

    if (isset(\UI\sess::$user)) {
      self::$values = \UI\sess::$user->site->get_setting('lus');
    }
    if (empty(self::$values)) {
      $fhandle = fopen(Config::get('prefix') . '/config/lus.csv.dist','r');
      self::$values = fgetcsv($fhandle);
    }

  } // _auto_init

}
?>
