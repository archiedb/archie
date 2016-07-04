<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Material extends database_object { 

	public $uid; 
	public $name; 
  public $enabled; // Boolean

	/**
	 * Constructor
	 * Takes a UID and returns a material from the database
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

    // passing array(false) causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `material` WHERE `material`.`uid` IN ' . $idlist;
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('material',$row['uid'],$row); 
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
	 * Refreshes the object from the db
	 */
	public function refresh() { 

		Material::remove_from_cache('material',$this->uid); 
		$this->__construct($this->uid); 
	} 

  /**
   * has_classification
   * Return true if classification is assoicated with this material and enabled
   */
	public function has_classification($classification) { 

		$material_id = Dba::escape($this->uid); 
		$classification = Dba::escape($classification); 
		$sql = "SELECT * FROM `material_classification` WHERE `material`='$material_id' AND `classification`='$classification'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results);  

		return $row['material']; 	

	} // has_classification

  /**
   * get_all
   * Returns all of the materials
   */
	public static function get_all() { 

    $results = array(); 

		$sql = "SELECT * FROM `material`"; 
		$db_results = Dba::read($sql); 
		while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('material',$row['uid'],$row); 
			$results[] = new Material($row['uid']); 
		} 

		return $results; 

	} // get_all

  /**
   * name_to_id
   * Takes a material name and returns the UID
   */
	public static function name_to_id($name) { 

		$name = Dba::escape($name); 

		$sql = "SELECT `uid` FROM `material` WHERE `name` LIKE '$name'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		return $row['uid']; 

	} // name_to_id

  /**
   * enable
   * Enable a material
   */
  public function enable() {

    $uid = Dba::escape($this->uid); 
    $sql = "UPDATE `material` SET `enabled`='1' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    return $db_results; 

  } // enable

  /**
   * disable
   * Disable a material
   */
  public function disable() { 
  
    $uid = Dba::escape($this->uid); 
    $sql = "UPDATE `material` SET `enabled`='0' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql); 

    return $db_results; 
  
  } // disable

  /**
   * create
   * Creates a new material
   */
  public static function create($input) { 

    // Reset the error state
    Err::clear(); 

    if (!Material::validate($input)) {
      Err::add('general','Invalid Input please check');
      return false;
    }

    // Yeah that was about it, we're good to go here
    $name = Dba::escape($input['name']); 
    $sql = "INSERT INTO `material` SET `name`='$name',`enabled`='0'";
    $db_results = Dba::write($sql); 

    if (!$db_results) { 
      Err::add('general','Database Write Filaure, please resubmit'); 
      return false; 
    }

    return Dba::insert_id();  

  } // create

  /**
   * validate
   * Make sure we've given valid input
   */
  public static function validate($input) { 

    // Make sure this is a unique name
    $uid = Material::name_to_id($input['name']);
    if ($uid AND ($uid != $input['material_id'])) {
      Err::add('name','Duplicate Material - name already exists');
      return false;
    }

    if (!strlen($input['name'])) { 
      Err::add('name','Name cannot be blank');
      return false;
    }

    if (Err::occurred()) { return false; }

    return true;

  } // validate

  /** 
   * update
   * This updates the name, and re-builds the Material<-->Classification mapping
   */
  public function update($input) { 

    Err::clear(); 

    if (!Material::validate($input)) {
      Err::add('general','Invalid Input please check');
      return false;
    }

    // Delete all of the current material->classification mappings, easier then figuring out what we missed
    $uid = Dba::escape($input['material_id']);
    $sql = "DELETE FROM `material_classification` WHERE `material`='$uid'";
    $db_results = Dba::write($sql); 

    foreach ($input['classification'] as $classification) {
      $classification = Dba::escape($classification);
      $uid = Dba::escape($input['material_id']);
      $sql = "INSERT INTO `material_classification` (`material`,`classification`) VALUES ('$uid','$classification')";
      $db_results = Dba::write($sql);
    }

    // Update the name of the material
    $uid = Dba::escape($input['material_id']);
    $name = Dba::escape($input['name']);
    $sql = "UPDATE `material` SET `name`='$name' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    // Record this change incase this is actually bad juju
    Event::record('Material','NAME CHANGE - Old ' . $this->name . ' --> New ' . $name);

    $this->refresh();

    return true;

  } // update

} // material
