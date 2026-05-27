<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Drawer extends database_object { 


	public $uid; 
	public $name; 
  public $enabled;
  public $institution;

	/**
	 * Constructor
	 * Takes a UID and pulls info from the database
	 */
	public function __construct($uid='') { 

    if (!is_numeric($uid)) { return false; }

		$row = $this->get_info($uid); 
		
		if (!is_array($row)) { return false; }
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

    $sql = 'SELECT * FROM `drawer` WHERE `drawer`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('drawer',$row['uid'],$row); 
    }

    return true; 
  
  } // build_cache

	/**
	 * refresh
	 * Refresh the object
	 */
	public function refresh() { 

		Drawer::remove_from_cache('drawer',$this->uid); 
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Display items publically 
   */
  public function _display($variable) {

  } // _display

  /**
   * get_all
   * Return all of the drawers
   */
	public static function get_all($enabled=true) { 

    if ($enabled === true) {
      $enabled_sql = "`enabled`='1'";
    }
    else {
      $enabled_sql = "1=1";
    }
		$sql = "SELECT * FROM `drawer` WHERE $enabled_sql"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('drawer',$row['uid'],$row); 
			$results[] = new Drawer($row['uid']); 
		} 

		return $results; 

	} // get_all

  /**
   * name_to_id
   * This returns an ID from a name
   */
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `drawer` WHERE `name` LIKE '$name'"; 

		$db_results = Dba::read($sql); 
		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

  /**
   * enable
   * Enable the drawer
   */
  public function enable() { 

    $uid = Dba::escape($this->uid);
    $sql = "UPDATE `drawer` SET `enabled`='1' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return $db_results;

  } // enable

  /**
   * disable
   * Disable the drawer
   */
  public function disable() { 

    $uid = Dba::escape($this->uid);
    $sql = "UPDATE `drawer` SET `enabled`='0' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return $db_results;

  } // disable


  /**
   * create
   * This is used for creating a new drawer
   */
  public static function create($input) { 

    // Reset the error state
    Err::clear();

    if (Drawer::name_to_id($input['name'])) { 
      Err::add('general','Duplicate Drawer - name already exists');
      return false;
    }

    if (strlen($input['name']) < 1) {
      Err::add('general','Name cannot be blank');
      return false;
    }

    // Ensure that the Institution exists, and is enabled
    $cabinet = new Cabinet($input['cabinet']);
    if (!$cabinet->enabled()) { 
      Err::add('general','Cabinet not enabled');
      return false;
    }

    // Nothing else to check... yet
    $sql = "INSERT INTO `drawer` SET `name`=?, `cabinet`=?, `enabled`='0'";
    $db_results = Dba::write($sql,array($input['name'],$input['institution']));

    return Dba::insert_id();

  } // create

} // drawer 
