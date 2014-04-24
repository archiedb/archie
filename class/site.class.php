<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Site extends database_object { 

	public $uid; 
  public $name;
  public $description;
  public $northing; 
  public $easting;
  public $elevation;
  public $pi; // site.principal_investigator
  public $partners; // text field
  public $excavation_start; // timestamp
  public $excavation_end; // timestamp
  public $enabled; 

	// Constructor takes a uid
	public function __construct($uid='') { 

		//if (!is_numeric($uid)) { return false; } 

    //FIXME: UID is the site name until we migrate
    // Hack it in until we have a database
    $table = array('1'=>array('uid'=>'10IH73','name'=>'10IH73','description'=>'Coopers Ferry')); 

		//$row = $this->get_info($uid,'site'); 
    $row = $table['1'];

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

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

    $sql = 'SELECT * FROM `site` WHERE `site`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('site',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

  /**
   * get_from_name
   * Take a sitename and return the object
   */
  public static function get_from_name($name) { 

    $name = Dba::escape($name); 

    $sql = "SELECT `uid` FROM `site` WHERE `name`='$name'";
    //$db_results = Dba::read($sql); 

    //$row = Dba::fetch_assoc($db_results);
    $row['uid'] = 1;
    return $row['uid'];

  } // get_from_name

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('site',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * create
   */
  public static function create($input) { 



  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if (Error::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * user_level
   * returns the access level for the specified user
   */
  public static function user_level($site_uid,$user_uid) { 

    //FIXME: We need a database to do anything meaninful here
    return true; 

  } // user_level

  /**
   * get_all
   * Return all of the sites
   */
  public static function get_all() { 

    $results = array(); 

    $sql = 'SELECT * FROM `site`';
    $db_results = Dba::read($sql); 
    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('site',$row['uid'],$row);
      $results[] = new Site($row['uid']); 
    }

    return $results;

  } // get_all

} // end class level
?>
