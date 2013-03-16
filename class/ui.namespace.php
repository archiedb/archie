<?php namespace UI;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * referrer
 * We have a specific list of urls we're allowed to redirect to!
 */
function return_url($input) {

  // We need to keep the original intact here, but #'s of an ID at the end are ok
  $check = rtrim($input,'0..9'); 
  $allowed_urls = array('/records/view/',
                    '/records/edit/',
                    '/users/view'); 

  if (in_array($check,$allowed_urls)) { 
    return $input;
  }

  if (!\Access::has('admin','admin')) { return '/'; }

  // If they are an administrator there are a few more urls they can redirect to
  $allowed_urls = array('/users/manage',
                  '/users/manage/disabled',
                  '/users/manage/online',
                  '/users/manage/all'); 

  if (in_array($check,$allowed_urls)) { 
    return $input;
  }

  return '/'; 

} // return url

/**
 * field_name
 * Takes a field, and returns the "name" for it (these don't match :S)
 */
function field_name($field) { 

  $names = array('catalog_id'=>'Catalog #',
      'station_index'=>'RN',
      'xrf_matrix_index'=>'XRF Matrix',
      'xrf_artifact_index'=>'XRF Artifact',
      'lsg_unit'=>'L.U',
      );

  if (in_array($field,array_keys($names))) { return $names[$field]; }

  return ucfirst($field); 

} // field_name

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
      return \Config::get('prefix') . '/template' . $name . '.inc.php'; 
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

    // Only users here!
    if (get_class($user) != 'User') { return false; }

    self::$user = $user; 

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
      case '4': 
        return isset(self::$location['4']) ? self::$location['4'] : false;
      break; 
      case 'absolute':
        $page = isset(self::$location['0']) ? self::$location['0'] : '';
        $action = isset(self::$location['1']) ? self::$location['1'] : '';
        $objectid = isset(self::$location['2']) ? self::$location['2'] : '';
        return rtrim('/' . $page . '/' . $action . '/' . $objectid,'/'); 
      break; 
    }

    return false; 

  } // url

} // sess

?>
