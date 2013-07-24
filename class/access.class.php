<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * Access
 * This handles the access control stuff for things
 */
class Access { 

  private static $types = array('user','record','admin','media','level','feature','krotovina','site'); 
  private static $actions = array('write','read','delete','admin','download'); 
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
   * record
   * Checks permissions on records
   */
  private static function check_record($action,$uid) { 

    // Only admins now?
    return false; 

  } // check_record

  /**
   * media
   * Checks permissions on media
   */
  private static function check_media($action,$uid) { 

    switch ($action) { 
      case 'download':
        if (\UI\sess::$user->access >= '50') { return true; }
      break;
      case 'write':
        if (\UI\sess::$user->access >= '50') { return true; }
      break;
    } 

    return false; 

  } // check_media

  /**
   * check_level
   * Make sure they are allowed to edit the level
   */
  private static function check_level($action,$uid) { 

      switch ($action) { 
        case 'read':
          return true; // You can always read?
        break;
        case 'write':
          // Must be an open level
          $level = new level($uid); 
          if ($level->closed) { return false; }
          else { return true; }
        break;
        case 'delete':
          return false; // No
        break;
      } 

      return false; 

  } // check_level

  /**
   * check_site
   * Return true/false based on action
   */
  private static function check_site($action,$uid) {

    switch ($action) { 
      case 'read':
        if (Site::user_level($uid,\UI\sess::$user->uid) == 5) { return true; }
      break;
      case 'write':
        if (Site::user_level($uid,\UI\sess::$user->uid) == 50) { return true; }
      break;
      case 'delete':
        return false; // Admins only!
      break;
    }

    return false; 

  } // check_site

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
