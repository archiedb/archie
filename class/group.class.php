<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Group extends database_object { 

	public $uid; 
	public $name; 
  public $description;
  public $roles; // Generated value from effective roles

	/**
	 * Constructor
	 * Takes a UID and pulls info from the database
	 */
	public function __construct($uid='') { 

    if (!is_numeric($uid)) { return false; }

		$row = $this->get_info($uid); 
		
		if (!count($row)) { return false; }

    foreach ($row as $key=>$value) { $this->$key = $value; }

    return true;

	} // constructor

  /**
    * build_cache
    * Build a cache of our objects, save some queries
    */
  public static function build_cache($objects) {
  
    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `group` WHERE `group`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('group',$row['uid'],$row); 
    }

    return true; 
  
  } // build_cache

	/**
	 * refresh
	 * Refresh the object
	 */
	public function refresh() { 

		Classification::remove_from_cache('group',$this->uid); 
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * User friendly display
   */
  public function _display($variable) { 



  } // _display

  /**
   * get_all
   * Return all of the groups
   */
	public static function get_all() { 

		$sql = "SELECT * FROM `group`"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('group',$row['uid'],$row); 
			$results[] = new Group($row['uid']); 
		} 

		return $results; 

	} // get_all

  /**
   * name_to_id
   * This returns an ID from a name
   */
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `group` WHERE `name` LIKE '$name'"; 

		$db_results = Dba::read($sql); 
		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

  /**
   * validate
   * Validate the group input
   */
  public static function validate($input) { 

    $retval = true;
    // If we've got no group set it to an impossible value, this is wrong
    if (!isset($input['group'])) { $input['grou['] = '-1'; }

    if (Group::name_to_id($input['name']) != $input['group']) { 
      Err::add('general','Duplicate Group - name already exists');
      $retval = false;
    }

    if (strlen($input['name']) < 1) {
      Err::add('general','Name cannot be blank');
      $retval = false;
    }

    return $retval;

  } // validate

  /**
   * create
   * This is used for creating a new group
   */
  public static function create($input) { 

    // Reset the error state
    Err::clear();

    if (!Group::validate($input)) { 
      return false;
    }

    // Nothing else to check... yet
    $name = Dba::escape($input['name']);
    $description = Dba::escape($input['description']);
    $sql = "INSERT INTO `group` SET `name`='$name', `description`='$description'";
    $db_results = Dba::write($sql);

    $insert_id = Dba::insert_id();

    if (!$insert_id) { 
      Err::add('general','Database Error creating group, please contact administrator');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * update
   * Update the group
   */
  public function update($input) { 

    Err::clear();

    if (!Group::validate($input)) { 
      return false;
    }

    $sql = 'UPDATE `group` SET `name`=?, `description`=? WHERE `uid`=?';
    $db_results = Dba::write($sql,array($input['name'],$input['description'],$this->uid));

    if (!$db_results) { 
      Event::error('Database','Unable to update group, check SQL');
    }

    return $db_results;

  } // update

  /**
   * set_roles
   * Get the current roles and action for this 
   */
  public function set_roles() {

    $sql = "SELECT `uid`,`role`,`action` FROM `group_role` WHERE `group`=?";
    $db_results = Dba::read($sql,array($this->uid));

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = array('uid'=>$row['uid'],'role'=>new Role($row['role']),'action'=>new Action($row['action']));
    }

    $this->roles = $results;

  } // set_roles

  /**
   * add_role
   * Adds a role/action to the current group
   */
  public function add_role($input) { 

    // Make sure it doesn't already exist
    $checksql = 'SELECT * FROM `group_role` WHERE `group`=? AND `role`=? AND `action`=?';
    $db_results = Dba::read($checksql,array($input['uid'],$input['role'],$input['action']));
    if ($row = Dba::fetch_assoc($db_results)) {
      Err::add('general','Attempted to add duplicate Role/Action');
      return false; 
    }

    // Make sure these are real roles/actions
    $action = new Action($input['action']);
    if (!$action->name) { 
      Err::add('general','Invalid Action specified');
      return false;
    }

    $role = new Role($input['role']);
    if (!$role->name) {
      Err::add('general','Invalid Role specified');
      return false;
    }

    // Make sure that this role/action combination is allowed
    $sql = "SELECT * FROM `role_action` WHERE `role`=? AND `action`=?";
    $db_results = Dba::read($sql,array($input['role'],$input['action']));
    if (!$row = Dba::fetch_assoc($db_results)) { 
      Err::add('general','Invalid Role/Action specified');
      return false;
    }

    // SQL for adding a new role/action
    $insertsql = 'INSERT INTO `group_role` (`group`,`role`,`action`) VALUES (?,?,?)';
    $db_results = Dba::write($insertsql,array($input['uid'],$input['role'],$input['action']));
    return $db_results;

  } // add_role

  /** 
   * delete_role
   * Removes a role from this group
   */
  public function delete_role($uid) {

    $sql = "DELETE FROM `group_role` WHERE `uid`=? AND `group`=?";
    $db_results = Dba::write($sql,array($uid,$this->uid));

    return $db_results;

  } //delete_role

  /**
   * delete
   * Attempts to delete a group
   */
  public static function delete($uid) { 

      // Remove the group roles
      $sql = "DELETE FROM `group_role` WHERE `group`=?";
      $db_results = Dba::write($sql,array($uid));

      // Remove from users
      $sql = "DELETE FROM `user_group` WHERE `group`=?";
      $db_results = Dba::write($sql,array($uid));

      // Remove group
      $sql = "DELETE FROM `group` WHERE `uid`=?";
      $db_results = Dba::write($sql,array($uid));

      return true;

  } // delete

} // group 
