<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

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
    if (!$this->name) { $this->name = $this->username; }

		return true; 

	} // constructor

  /**
   * build_cache
   */
  public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `users` WHERE `users`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('users',$row['uid'],$row); 
    }

    return true; 


  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('users',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

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
 	 * get
	 * This returns an array of user objects for every user as
	 * defined by the constraint
	 */
	public static function get($constraint='') { 
	
		$constraint_sql = ''; 

		switch ($constraint) { 
			case 'enabled':
				$constraint_sql = " AND `disabled` IS NULL";
			break;
			case 'disabled':
				$constraint_sql = " AND `disabled`='1'";	
			break; 
      case 'online': 
        $now = time(); 
        $constraint_sql = " AND `username` IN (SELECT `username` FROM `session` WHERE `expire` > '$now')"; 
			default: 
				// None!
			break;
		} 

		$users = array(); 

		$sql = 'SELECT `uid` FROM `users` WHERE 1=1' . $constraint_sql . " ORDER BY `name`";
		$db_results = Dba::read($sql); 

		while ($row = Dba::fetch_assoc($db_results)) { 
			$users[] = new User($row['uid']); 
		} 
		return $users; 

	} // get

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
	 * update
	 * takes input in the form of an array and updates the user
	 */
	public function update($input) { 

    // Feed some info in
    $input['username'] = $this->username; 

    // Clear the error before we validate
    Error::clear(); 

    // Validate input
    if (!User::validate($input)) {
      Error::add('general','Invalid Field Values - please check input'); 
      return false; 
    } 

		$uid = Dba::escape($this->uid); 
		$name = Dba::escape($input['name']); 
		$email = Dba::escape($input['email']); 
    $access = (Access::has('user','admin') === true) ? Dba::escape($input['access']) : Dba::escape($this->access); 

		$sql = "UPDATE `users` SET `access`='$access', `name`='$name', `email`='$email' WHERE `uid`='$uid' LIMIT 1"; 
		$db_results = Dba::write($sql); 

		// If this is the current logged in user, refresh them
		if (\UI\sess::$user->uid == $this->uid) { 
      \UI\sess::$user->refresh(); 
		} 

    return true; 

	} // update

	/**
	 * create
	 * This creates a new user! hooray
	 */
	public static function create($input) { 

    // Reset the error state before we start checking
    Error::clear(); 

    // Validate input
    if (!User::validate($input)) {
      Error::add('general','adding new user'); 
      return false; 
    } 
   
    // This is here because we only check on the creation of a user 
    if (strlen($input['password']) < 2) { 
      Error::add('password','Password not long enough'); 
      return false; 
    }

    $username = Dba::escape($input['username']); 
    $name = Dba::escape($input['name']); 
    $email = Dba::escape($input['email']); 
    $password = Dba::escape(hash('sha256',$input['password'])); 
    $access = Dba::escape($input['access']); 

    $sql = "INSERT INTO `users` (`name`,`username`,`email`,`password`,`access`) VALUES ('$name','$username','$email','$password','$access')"; 
    $db_results = Dba::write($sql); 

    if (!$db_results) { 
      Event::error('DATABASE','Error unable to insert user into database'); 
      Error::add('general','Unknown Error please contact Administrator'); 
      return false; 
    } 
    $insert_id = Dba::insert_id(); 

    return $insert_id; 

	} // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if ($input['password'] != $input['confirmpassword']) {
      Error::add('password','Passwords do not match'); 
    } 

    if (intval($input['access']) != $input['access']) { 
      Error::add('access','Invalid Access Level'); 
    }

    if (!strlen($input['name'])) { 
      Error::add('name','Display Name is a required field'); 
    }

    if (!strlen($input['username'])) { 
      Error::add('username','Username is a required field'); 
    }

    // Make sure that the username doesn't already exist
    $user = User::get_from_username($input['username']); 
    if ($user->uid AND $user->uid != $input['uid']) {
      Error::add('username','Username already exists, duplicate usernames not allowed');
    }

    if (Error::occurred()) { return false; }

    return true; 

  } // validate

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
