<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

class User extends database_object { 

	public $uid; 
	public $username; 
	public $name; 
	public $email; 
	public $access; 
	public $disabled; 
	public $password; // SHA2 (256)

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'users'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 
		// Don't actually keep this in the object 
		unset($this->password); 

		return true; 

	} // constructor

	// Return the user based on the username
	public static function get_from_username($username) { 

		$username = Dba::escape($username); 
		$sql = "SELECT `uid` FROM `users` WHERE `username`='$username'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		$user = new User($row['uid']); 

		return $user; 

	} // get_from_username

	/**
 	 * get_all
	 * This returns an array of user objects for every user
	 */
	public static function get_all() { 

		$users = array(); 

		$sql = "SELECT `uid` FROM `users` ORDER BY `username`"; 
		$db_results = Dba::read($sql); 

		while ($row = Dba::fetch_assoc($db_results)) { 
			$users[] = new User($row['uid']); 
		} 

		return $users; 

	} // get_all

	/**
	 * get_access_name
	 * This returns a friendly name for the int access level
	 */
	public static function get_access_name($access) { 

		switch ($access) { 
			case '100': 
				return "Admin";
			break;
			default:
				return "User";
			break; 
		} 

	} // get_access_name

	/**
	 * set_password
	 * This sets the password for the current user
	 */
	public function set_password($input) { 

		if (!strlen($input)) { 
			Event::error('User::set_password','Error no password specified'); 
			return false; 
		} 

		$password = Dba::escape(hash('sha256',$input)); 
		$uid = Dba::escape($this->uid); 

		$sql = "UPDATE `users` SET `password`='$password' WHERE `uid`='$uid'"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Event::error('User::set_password','SQL Error, password update failed'); 
			return false; 
		} 

		return true; 

	} // set_password

	/**
 	 * disable
	 * There should be some checking on this one...
	 */
	public function disable() { 

		$uid = Dba::escape($this->uid); 

		$sql = "UPDATE `users` SET `disabled`='1' WHERE `uid`='$uid'"; 
		$db_results = Dba::write($sql); 

		return true; 

	} // disable
	
	/**
 	 * enable
	 * Enable the user
	 */
	public function enable() { 

		$uid = Dba::escape($this->uid); 

		$sql = "UPDATE `users` SET `disabled`=NULL WHERE `uid`='$uid'"; 
		$db_results = Dba::write($sql); 

		return true; 

	} // enable

} // end class user
?>
