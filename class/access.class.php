<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * Access
 * This handles the access control stuff for things
 */
class Access { 

  private static $types = array('user'); 
  private static $actions = array('write','read','delete','admin'); 

  private function __construct() {}
  private function __clone() {}

  /**
   * has
   * returns true/false if the current logged in user has access to the thing
   * we are asking about (string)
   */
  public static function has($type,$action='',$uid=0) {

    if (!in_array($type,self::$types)) { 
      Event::error('ACCESS','Unknown access type [' . $type . ']'); 
      return false; 
    }  

    // If they are a site admin then return true
    if (\UI\sess::$user->access == '100') { return true; }
print_r(\UI\sess::$user); 
    $retval = self::{'check_' . $type}($action,$uid);

    return $retval; 

  } // has

  /**
   * user
   * checking user permissions
   */
  private static function check_user($action,$uid) { 

    $allowed_actions = array(); 

    // If this is the person who is logged in
    if ($uid == \UI\sess::$user->uid) { 
      $allowed_actions = array('read','write'); 
    }

    if (in_array($action,$allowed_actions)) { return true; }

    return false; 

  } // check_user


} // Access
