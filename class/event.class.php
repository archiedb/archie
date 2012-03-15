<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

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

		log_event($username,$topic,$content,'error'); 

	} 

	public static function record($topic,$content) { 

		log_event($GLOBALS['user']->username,$topic,$content,'record'); 
	} 

} // end class event
?>
