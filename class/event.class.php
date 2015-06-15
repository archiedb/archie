<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:
class Event { 

  private static $_events=array(); 

  private function __construct() { }
  private function __clone() {}

  private static $_allowed_severity = array('error','success','info','warning');

  /**
   * add
   * Add an event that has occured
   */
  public static function add($severity,$message,$size='') { 

    if (!in_array($severity,self::$_allowed_severity)) { return false; }
    if (!strlen($message)) { return false; } 
    
    self::$_events[] = array('severity'=>$severity,'message'=>$message,'size'=>$size); 
    $_SESSION['events'] = self::$_events; 
    
    if (defined('CLI')) {
      self::record('UI-Error',json_encode(array('severity'=>$severity,'message'=>$message,'size'=>$size)));
    }

    return true; 

  } // add

  /**
   * auto_init
   */
  public static function init() { 

    // Check for session events
    self::$_events = isset($_SESSION['events']) ? $_SESSION['events'] : array(); 

  } // init

  /**
   * display
   * Display the requested event,reset it after we're done
   */
  public static function display($type='events') { 
    $message = '';
    switch ($type) { 
      case 'errors':
        if (!Error::occurred()) { return false; }
        $errors = Error::get_all(); 
        if (isset($errors['general'])) { 
          $header_small = ' ' . scrub_out($errors['general']);
          unset($errors['general']); 
        }
        foreach ($errors as $key=>$value) { 
          $message .= "<dl><dt>" . \UI\field_name($key) . "</dt><dd>$value</dd></dl>";
        }
        $css_class = ' alert-error';
        $header = '<h4>Error:' . $header_small . '</h4>';
        $size = ' alert-block';
        require \UI\template('/event'); 
      break; 
      case 'warnings':
        $warnings = Error::get_all('warnings'); 
        if (!count($warnings)) { return false; }
        if (isset($warnings['general'])) { 
          $header_small = ' ' . scrub_out($warnings['general']);
          unset($warnings['general']); 
        }
        foreach ($warnings as $key=>$value) { 
          $message .= "<dl><dt>" . \UI\field_name($key) . "</dt><dd>$value</dd></dl>";
        }
        $css_class = ''; 
        $header = '<h4>Warning:' . $header_small . '</h4>';
        $size = ' alert-block';
        require \UI\template('/event'); 
      break; 
      default:
      case 'events':
        if (!count(self::$_events)) { return false; }

        // Show the event under this name
        foreach (self::$_events as $event) { 
          $message = scrub_out($event['message']); 
          $css_class = ($event['severity'] == 'warning') ? '' : ' alert-' . $event['severity'];
          $header = ($event['size'] == 'small') ? '<strong>' .ucfirst($event['severity']) . ':</strong>' : '<h4>' . ucfirst($event['severity']) . '</h4>';
          $size = ($event['size'] == 'small') ? '' : ' alert-block';

          require \UI\template('/event'); 

        } // end foreach events

        self::$_events=array(); 
        $_SESSION['events'] = self::$_events; 
      break; 
    } // end switch

  } // display

	/**
 	 * error
	 * Events that are errors
	 */
	public static function error($topic,$content) { 

    if (defined('NO_LOG')) { return true; }
    $username = is_object(\UI\sess::$user) ? \UI\sess::$user->username : 'SYSTEM';
		log_event($username,$topic,$content,'error'); 

	} // error 

  /**
   * record
   * keep a written record of events
   */
	public static function record($topic,$content,$logname='record') { 

    if (defined('NO_LOG')) { return true; }
    $username = is_object(\UI\sess::$user) ? \UI\sess::$user->username : 'SYSTEM';
  	log_event($username,$topic,$content,$logname); 

	} // record 

} // end class event
?>
