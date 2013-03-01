<?php namespace UI;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * boolean_word
 * Take a T/F value and return a pretty response
 */
function boolean_word($boolean,$string='') { 

  if ($string == '') { 
    $string = $boolean ? 'True' : 'False';
  }

  if ($boolean) { 
    return '<span class="label label-success">' . $string . '</span>';
  }
  else {
    return '<span class="label label-important">' . $string . '</span>';
  }

  return false; 

} // boolean_word

/**
 * template
 * Returns the full filename for the template
 * uses sess::location() to figure it out
 */
function template($name='') { 

    if (strlen($name)) {
      return \Config::get('prefix') . '/' . $name; 
    }

    $filename = \Config::get('prefix') . '/template'; 
    $filename .= sess::location('page') ? '/' . sess::location('page') : '';
    $filename .= sess::location('action') ? '/' . sess::location('action') : ''; 

    // Add extension
    $filename .= '.inc.php';

    return $filename;     

} // template

/**
 * sess
 * This is a static class that holds our current session state, not sure if 
 * this is the right way to do it, but it's better then globals
 */
class sess {

  public static $user; // our currently logged in user
  private static $location=array(); // clean-url stuff

  private function __construct() {}
  private function __clone() { }

  public static function set_user($user) { 

    // Technically they can overwrite this from outside
    if (!self::$user) { 
      self::$user = $user; 
    }
    else {
      Event:error('OVERWRITE','Attempted to overwrite the session user'); 
      return false; 
    } 

    return true; 

  } // set user

  /**
   * set_location
   * takes care of parsing out our url
   */
  public static function set_location($uri) { 

    $urlvar = explode('/',$uri);
    $www_prefix = explode('/',rtrim(\Config::get('web_prefix'),'/'));
    foreach ($www_prefix as $prefix) { 
      array_shift($urlvar); 
    } 

    self::$location = $urlvar;

  } // set_location

  /**
   * location
   * return the specified part of the clean url
   */
  public static function location($section) { 

    switch ($section) { 
      case '0':
      case 'page':
        return isset(self::$location['0']) ? self::$location['0'] : ''; 
      break;
      case '1':
      case 'action':
        return isset(self::$location['1']) ? self::$location['1'] : false;
      break;
      case '2':
      case 'objectid':
        return isset(self::$location['2']) ? self::$location['2'] : false;
      break;
      case '3':
        return isset(self::$location['3']) ? self::$location['3'] : false;
      break;
    }

    return false; 

  } // url

} // sess

?>
