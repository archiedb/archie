<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Feature extends database_object { 

	public $uid; 
  public $site; // FK Site
  public $record; // FK Record
  public $keywords;
  public $description;
  public $user; // FK User
  public $created;
  public $updated;
  public $closed;
  public $closed_date;
  public $closed_user; // FK User

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'feature'); 

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

    $sql = 'SELECT * FROM `feature` WHERE `feature`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('feature',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('feature',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * create
   */
  public static function create($input) { 

    Error::clear();

    // Force the site to the current users site
    $input['site'] = \UI\sess::$user->site->uid;

    if (!Feature::validate($input)) {
      Error::add('general','Invalid Field Values - please check input');
      return false;
    }

    // Record (id for bag) is generated here

  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 


    // Site - set by config
    // record_id 
    //   K-????? for Krotovina
    //   F-????? for feature
    //   ??? for Level
    // quad - NW/NE (list)
    // unit - A-Z (list)
    // northing - 8,3 decimal
    // easting - ''
    // type - Feature, Krotovina, Level
    // elv_* are same as northing
    
    if (Error::occurred()) { return false; }

    return true; 

  } // validate


} // end class level
?>
