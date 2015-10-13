<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Role extends database_object { 

	public $uid; 
	public $name; 
  public $description;

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

    $sql = 'SELECT * FROM `role` WHERE `role`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('role',$row['uid'],$row); 
    }

    return true; 
  
  } // build_cache

  /**
   * _display
   */
  public function _display($variable) { 


  } // _display

	/**
	 * refresh
	 * Refresh the object
	 */
	public function refresh() { 

		Role::remove_from_cache('role',$this->uid); 
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Show the pretty version of things
   */
  public function _display($variable) { 

  
  } // _display

  /**
   * name_to_id
   * This returns an ID from a name
   */
	public static function name_to_id($name) { 

		$sql = 'SELECT `uid` FROM `role` WHERE `name` LIKE ?'; 
		$db_results = Dba::read($sql,array($name)); 
		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

  /**
   * get_all
   * Return an array of all possible roles
   */
  public static function get_all() { 

    $sql = 'SELECT `uid` FROM `role`';
    $db_results = Dba::read($sql);
    
    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid'];
    }

    // This is faster then individual builds of each role
    self::build_cache($results);

    foreach ($results as $uid) { 
      $objects[] = new Role($uid);
    }

    return $objects;

  } //get_all

} // role 
