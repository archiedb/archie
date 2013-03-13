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
  public static function display() { 

    if (!count(self::$_events)) { return false; }

    // Show the event under this name
    foreach (self::$_events as $event) { 
      $message = $event['message'];
      $css_class = ($event['severity'] == 'warning') ? '' : ' alert-' . $event['severity'];
      $header = ($event['size'] == 'small') ? '<strong>' .ucfirst($event['severity']) . ':</strong>' : '<h4>' . ucfirst($event['severity']) . '</h4>';
      $size = ($event['size'] == 'small') ? '' : ' alert-block';

      require \UI\template('/event'); 

    } // end foreach events

    self::$_events=array(); 
    $_SESSION['events'] = self::$_events; 

  } // display

	/**
 	 * error
	 * Events that are errors
	 */
	public static function error($topic,$content) { 

		$username = 'SYSTEM'; 
    if (is_object(\UI\sess::$user)) {
			$username = \UI\sess::$user->username;
		} 

    if (defined('NO_LOG')) { return true; }
		log_event($username,$topic,$content,'error'); 
	} // error 

  /**
   * record
   * keep a written record of events
   */
	public static function record($topic,$content) { 

    if (!defined('NO_LOG')) {
  		log_event(\UI\sess::$user->username,$topic,$content,'record'); 
    }
	} // record 

} // end class event
?>
