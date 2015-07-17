<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class User extends database_object { 

	public $uid; 
	public $username; 
	public $name; 
	public $email; 
	public $access; // DEAD 
  public $roles; // Pulled from user_permission_view
  public $site; // UID of their current site
  public $last_login;
	public $disabled; 
	public $password; // SHA2 (256)

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'users'); 
    if (is_array($row)) { 
    	foreach ($row as $key=>$value) { 
    		$this->$key = $value; 
    	} 
    }
		// Don't actually keep this in the object 
		if (property_exists($this,'password')) { unset($this->password); }

    // Load their roles
    if (!isset($row['roles'])) { $row['roles'] = false; }
    if (!is_array($row['roles'])) {
      $this->roles = User::get_roles($this->uid,$this->site);
      $row['roles'] = $this->roles;
      parent::add_to_cache('users',$uid,$row);
    }

    if ($this->site) { $this->site = new Site($this->site); }

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

    foreach ($objects as $uid) {
      $roles[$uid] = array();
    }

    // Build the roles cache
    $sql = 'SELECT * FROM `user_permission_view` WHERE `user` IN ' . $idlist;
    $db_results = Dba::read($sql);

    while ($row = Dba::fetch_assoc($db_results)) { 
      $roles[$row['user']][$row['role']] = $row['action'];
    }

    $sql = 'SELECT * FROM `users` WHERE `users`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      // If they have no role, give them an empty one so it's recongized as cached
      if (!isset($roles[$row['uid']])) { $roles[$row['uid']] = array(); }
      $row['roles'] = $roles[$row['uid']];
      parent::add_to_cache('users',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

  /**
   * get_roles
   * Get the access roles for this user
   */
  public static function get_roles($uid,$site='') {

    $site = strlen($site) ? $site : \UI\sess::$user->site->uid;

    $sql = "SELECT * FROM `user_permission_view` WHERE `user`=? AND (`site`=? OR `site`='0')";
    $db_results = Dba::read($sql,array($uid,$site));

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[$row['role']][$row['action']] = true;
    }
    
    return $results;

  } // get_roles

  /**
   * add_group
   * Add this user to the specified access group
   */
  public function add_group($groupuid) {

    // Make sure they aren't already in the group
    $sql = "SELECT * FROM `user_group` WHERE `user`=? AND `group`=? AND `site`=?";
    $db_results = Dba::read($sql,array($this->uid,$groupuid,\UI\sess::$user->site->uid));
    if ($row = Dba::fetch_assoc($db_results)) {
      Error::add('general','User already in group');
      return false;
    }

    $group = new Group($groupuid);
    if (!$group->name) {
      Error::add('general','Invalid Group specified');
      return false;
    }

    $sql = "INSERT INTO `user_group` (`user`,`group`,`site`) VALUES (?,?,?)";
    $db_results = Dba::write($sql,array($this->uid,$groupuid,\UI\sess::$user->site->uid));

    return $db_results;

  } // add_group

  /**
   * delete_group
   */
  public function delete_group($group) { 

    $sql = "DELETE FROM `user_group` WHERE `user`=? AND `group`=? AND `site`=?";
    $db_results = Dba::write($sql,array($this->uid,$group,\UI\sess::$user->site->uid));

    return $db_results;
    
  } // delete_group

  /**
   * get_groups
   * Returns all groups this user is in
   */
  public function get_groups() { 

    $sql = "SELECT `group` FROM `user_group` WHERE `user`=? AND `site`=?";
    $db_results = Dba::read($sql,array($this->uid,\UI\sess::$user->site->uid));

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = new Group($row['group']);
    }

    return $results; 
  } // get_groups

  /**
   * get_sites
   * Returns all sites this user has permission to
   */
  public function get_sites() { 

      // If they have admin/admin they can have them all
      if (Access::has('admin','admin')) { 
        $sql = "SELECT `uid` AS `site` FROM `site`";
      }
      else { 
        $uid = Dba::escape($this->uid);
        $sql = "SELECT `site` FROM `user_group` WHERE `user`='$uid'";
      }
      $db_results = Dba::read($sql); 
      $sites = array(); 

      while ($row = Dba::fetch_assoc($db_results)) { 
        $sites[] = new Site($row['site']);
      }

      return $sites; 

  } // get_sites

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

		$sql = "SELECT * FROM `users` WHERE `username`=?"; 
		$db_results = Dba::read($sql,array($username)); 

		$row = Dba::fetch_assoc($db_results); 
    if (isset($row['uid'])) { 
      parent::add_to_cache('users',$row['uid'],$row);
		  $user = new User($row['uid']); 
    }
    else {
      $user = new User(false);
    }

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
      case 'all':
        $constraint_sql = " AND 1=1";
      break;
      case 'online': 
        $now = time(); 
        $constraint_sql = " AND `username` IN (SELECT `username` FROM `session` WHERE `expire` > '$now')"; 
			default: 
				// None!
			break;
		} 

		$users = array(); 

		$sql = 'SELECT * FROM `users` WHERE 1=1' . $constraint_sql . " ORDER BY `name`";
		$db_results = Dba::read($sql); 

		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('users',$row['uid'],$row);
			$users[] = new User($row['uid']); 
		} 
		return $users; 

	} // get

	/**
	 * set_password
	 * This sets the password for the current user
	 */
	public function set_password($input) { 

    if (!$this->uid) {
      Event::error('User::uid','No UID specified');
      return false;
    }

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

		$sql = 'UPDATE `users` SET `site`=?, `name`=?, `email`=? WHERE `uid`=? LIMIT 1'; 
		$db_results = Dba::write($sql,array($input['site'],$input['name'],$input['email'],$uid)); 

		// If this is the current logged in user, refresh them
		if (\UI\sess::$user->uid == $this->uid) { 
      \UI\sess::$user->refresh(); 
		} 

    return true; 

	} // update

  /**
   * update_site
   * Sets the users current site
   */
  public function update_site($site) {

    $sql = "UPDATE `users` SET `site`=? WHERE `uid`=? LIMIT 1";
    $db_results = Dba::write($sql,array($site,$this->uid));

 		// If this is the current logged in user, refresh them
		if (\UI\sess::$user->uid == $this->uid) { 
      \UI\sess::$user->refresh(); 
		} 

    return true; 

  } // update_site

  /**
   * update_last_seen
   * Update the last_seen info
   */
  public function update_last_seen() {

    $sql = 'UPDATE `users` SET `last_login`=? WHERE `uid`=?';
    $db_results = Dba::write($sql,array(time(),$this->uid));

  } // update_last_seen

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

    $password = hash('sha256',$input['password']); 
    $site = isset($input['site']) ? $input['site'] : \UI\sess::$user->site->uid;

    $sql = "INSERT INTO `users` (`name`,`username`,`email`,`password`,`site`) VALUES (?,?,?,?,?)"; 
    $db_results = Dba::write($sql,array($input['name'],$input['username'],$input['email'],$password,$site)); 

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
