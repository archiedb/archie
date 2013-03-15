<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * Access
 * This handles the access control stuff for things
 */
class Access { 

  private static $types = array('user','image','record','admin','media'); 
  private static $actions = array('write','read','delete','admin'); 
  private static $levels = array('0'=>'User','50'=>'Manager','100'=>'Admin'); 

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

    $check_name = 'check_' . $type; 
    $class_name = 'Access';
    if (method_exists($class_name,$check_name)) { 
      $retval = call_user_func($class_name . '::' . $check_name,$action,$uid); 
    }

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

  /**
   * image
   * This checks image perms
   */
  private static function check_image($action,$uid) { 

    // For now everyone has read
    switch ($action) { 
      case 'write':
        if (\UI\sess::$user->uid) { return true; }
      break;
      default:

      break;
    }

    // Right now, only admins
    return false; 

  } // check_image

  /**
   * record
   * Checks permissions on records
   */
  private static function check_record($action,$uid) { 

    // Only admins now?
    return false; 

  } // check_record

  /**
   * get_levels
   * Returns an array of possible levels
   */
  public static function get_levels() { 

    return self::$levels; 

  } // get_levels

  /**
   * get_level_name
   */
  public static function get_level_name($int) { 

    if (!isset(self::$levels[$int])) { return false; }

    return self::$levels[$int];

  } // get_level_name

} // Access
