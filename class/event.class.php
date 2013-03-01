<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:
class Event { 

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
	} 

	public static function record($topic,$content) { 
    if (!defined('NO_LOG')) {
  		log_event(\UI\sess::$user->username,$topic,$content,'record'); 
    }
	} 

} // end class event
?>
