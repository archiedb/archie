<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:
class Event { 

	/**
 	 * error
	 * Events that are errors
	 */
	public static function error($topic,$content) { 

		$username = 'SYSTEM'; 

		if (is_object($GLOBALS['user'])) { 
			$username = $GLOBALS['user']->username;
		} 
    if (!defined('NO_LOG')) {
  		log_event($username,$topic,$content,'error'); 
    }
	} 

	public static function record($topic,$content) { 
    if (!defined('NO_LOG')) {
  		log_event($GLOBALS['user']->username,$topic,$content,'record'); 
    }
	} 

} // end class event
?>
