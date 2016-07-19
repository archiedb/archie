<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Classification extends database_object { 


	public $uid; 
	public $name; 

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

    $sql = 'SELECT * FROM `classification` WHERE `classification`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('classification',$row['uid'],$row); 
    }

    return true; 
  
  } // build_cache

	/**
	 * refresh
	 * Refresh the object
	 */
	public function refresh() { 

		Classification::remove_from_cache('classification',$this->uid); 
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Display items publically 
   */
  public function _display($variable) {

  } // _display

	public static function get_from_material($material) { 

		$material = Dba::escape($material); 
		$sql = "SELECT `classification` FROM `material_classification` WHERE `material`='$material'"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		
		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = new Classification($row['classification']); 
		} 

		return $results; 

	} // get_from_material 

  /**
   * get_all
   * Return all of the classifications
   */
	public static function get_all() { 

		$sql = "SELECT * FROM `classification`"; 
		$db_results = Dba::read($sql); 

		$results = array(); 
		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('classification',$row['uid'],$row); 
			$results[] = new Classification($row['uid']); 
		} 

		return $results; 

	} // get_all

  /**
   * name_to_id
   * This returns an ID from a name
   */
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `classification` WHERE `name` LIKE '$name'"; 

		$db_results = Dba::read($sql); 
		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

  /**
   * enable
   * Enable the classification
   */
  public function enable() { 

    $uid = Dba::escape($this->uid);
    $sql = "UPDATE `classification` SET `enabled`='1' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return $db_results;

  } // enable

  /**
   * disable
   * Disable the classification
   */
  public function disable() { 

    $uid = Dba::escape($this->uid);
    $sql = "UPDATE `classification` SET `enabled`='0' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    return $db_results;

  } // disable


  /**
   * create
   * This is used for creating a new classification
   */
  public static function create($input) { 

    // Reset the error state
    Err::clear();

    if (Classification::name_to_id($input['name'])) { 
      Err::add('general','Duplicate Classification - name already exists');
      return false;
    }

    if (strlen($input['name']) < 1) {
      Err::add('general','Name cannot be blank');
      return false;
    }

    // Nothing else to check... yet
    $name = Dba::escape($input['name']);
    $description = Dba::escape($input['description']);
    $sql = "INSERT INTO `classification` SET `name`='$name', `description`='$description', `enabled`='0'";
    $db_results = Dba::write($sql);

    return Dba::insert_id();

  } // create

} // classification 
