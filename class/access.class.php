<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * Access
 * This handles the access control stuff for things
 */
class Access { 

  private static $types = array('user','media','record','admin','reports','level','feature','krotovina','site'); 
  private static $actions = array('create','read','delete','admin','manage','reopen','edit'); 

  private function __construct() {}
  private function __clone() {}

  /**
   * has
   * returns true/false if the current logged in user has access to the thing
   * we are asking about (string)
   */
  public static function has($type,$action='') {

    if (!in_array($type,self::$types)) { 
      Event::error('ACCESS','Unknown access type [' . $type . ']'); 
      return false; 
    }  

    // If they are a site admin then return true
    if (isset(\UI\sess::$user->roles['admin'])) {
      if (\UI\sess::$user->roles['admin']['admin'] === true) { return true; }
    }

    // If no action specified then any access is sufficient
    if ($action == '' AND isset(\UI\sess::$user->roles[$type])) { return true; }

    if (isset(\UI\sess::$user->roles[$type][$action])) {
      if (\UI\sess::$user->roles[$type][$action] === true) { return true; }
    }

    return false;

  } // has

  /**
   * is_admin
   * Return true if the person is a full admin
   */
  public static function is_admin() { 

    if (isset(\UI\sess::$user->roles['admin'])) {
      if (\UI\sess::$user->roles['admin']['admin'] === true) { return true; }
    }

    return false; 

  } // is_admin

} // Access
