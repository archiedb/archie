<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Site extends database_object { 

  public $uid; 
  public $name;
  public $description;
  public $northing; 
  public $easting;
  public $elevation;
  public $principal_investigator; // site.principal_investigator
  public $partners; // text field
  public $excavation_start; // timestamp
  public $excavation_end; // timestamp
  public $units; // from Settings
  public $quads; // from Settings
  public $ticket; // from Settings
  public $enabled; 

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 
    // By default don't re-cache
    $recache = false;

		$row = $this->get_info($uid,'site'); 
    if (is_array($row)) {
  		foreach ($row as $key=>$value) { 
  			$this->$key = $value; 
  		} 
    }
    else { return false; }

    // Get the project and accession - may be cached
    if (!property_exists($this,'project')) { 
      $this->project = Site::get_data($uid,'project');
      $row['project'] = $this->project;
      $recache = true;
    }
    if (!property_exists($this,'accession')) { 
      $this->accession = Site::get_data($uid,'accession');
      $row['accession'] = $this->accession;
      $recache = true;
    }

    // Decode settings
    $recache = $this->decode_settings() ? true : $recache;

    if ($recache === true) {
      parent::add_to_cache('site',$row['uid'],$row);
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
      $row['project'] = Site::get_data($row['uid'],'project');
      $row['accession'] = Site::get_data($row['uid'],'accession');
      parent::add_to_cache('site',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

  /**
   * decode_settings
   * This takes the ->settings field from the DB
   * runs a json_decode() and does what it needs to
   */
  public function decode_settings() { 

    $settings = json_decode($this->settings,true); 

    // Check for some defaults, load from dist file if none set
    if (!isset($settings['units'])) { 
      $settings['units'] = fgetcsv(fopen(Config::get('prefix') . '/config/units.csv.dist','r'));
    }
    if (!isset($settings['quads'])) {
      $settings['quads'] = fgetcsv(fopen(Config::get('prefix') . '/config/quads.csv.dist','r'));
    }
    if (!isset($settings['ticket'])) { 
      $settings['ticket'] = Config::get('ticket_size');
    }

    // Set
    $this->quads = $settings['quads'];
    $this->units = $settings['units'];
    $this->ticket = $settings['ticket'];

    return true; 

  } // decode_settings

  /**
   * update_settings
   * json_encode() and update db
   */
  public function update_settings($input) { 

    // Validate settings
    if (!$this->validate_settings($input)) {
      Error::add('general','Invalid Settings specified');
      return false;
    }

    // Setup the new array
    $settings = array();
    $settings['quads'] = isset($input['quads']) ? explode(',',$input['quads']) : $this->quads; 
    $settings['units'] = isset($input['units']) ? explode(',',$input['units']) : $this->units; 
    $settings['ticket'] = isset($input['ticket']) ? $input['ticket'] : $this->ticket; 

    $sql = "UPDATE `site` SET `settings`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array(json_encode($settings),$this->uid));

    return true;

  } // update_settings

  /**
   * validate_settings
   * validate the settings
   */
  public function validate_settings($input) { 

    switch ($input['key']) {
      case 'ticket':
        // only allow valid tickets
        $tickets = get_class_methods('genpdf');
        $method_name = 'ticket_' . $input['ticket'];
        if (in_array($method_name,$tickets)) { return true; }
        else { return false; }
      break;
      case 'units':
        $invalid_units = '';
        $retval = true;
        // Must be a csv, and only A-Z,0-9,_,-? 
        $units = explode(',',$input['units']);
        foreach ($units as $unit) {
          if (preg_match('/[^a-z_\-0-9]/i',$unit)) {
            $invalid_units .= $unit . ' :: ';
            $retval = false;
          }
        }
        if (!$retval) { Error::add('units','Invalid Units, only A-Z,0-9,_,- allowed, Invalid Unit(s) - ' . $invalid_units); }
        return $retval; 
      break;
      case 'quads':
        // Must be a csv, and only A-Z,0-9,_,-?
        $invalid_quads = '';
        $retval = true;
        $quads = explode(',',$input['quads']);
        foreach ($quads as $quad) {
          if (preg_match('/[^a-z_\-0-9]/i',$quad)) {
            $retval = false;
            $invalid_quads = $quad . ' :: ';
          }
        } 
        if (!$retval) { Error::add('quads','Invalid Quads, only A-Z,0-9,_,- allowed, Invalid Quad(s) - '. $invalid_quads); }
        return $retval;
      break;
    }

    return false;

  } // validate_settings

  /**
   * get_from_name
   * Take a sitename and return the object
   */
  public static function get_from_name($name) { 

    $name = Dba::escape($name); 

    $sql = "SELECT `uid` FROM `site` WHERE `name`='$name'";
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results);
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
   * get_all_data
   */
  public function get_all_data($field) {

    $allowed_fields = array('project','accession');

    if (!in_array($field,$allowed_fields)) { 
      return false;
    }

    $sql = "SELECT `value` AS ?,`created`,`closed` FROM `site_data` WHERE `site`=? AND `key`=? ORDER BY `created` DESC";
    $db_results = Dba::read($sql,array($field,$this->uid,$field));

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row;
    }

    return $results; 

  } // get_all_data

  /**
   * get_data
   */
  public static function get_data($site,$field) {

    $allowed_fields = array('project','accession');

    if (!in_array($field,$allowed_fields)) {
      return false;
    }

    $sql = "SELECT `value` FROM `site_data` WHERE `site`=? AND `key`=? AND `closed` IS NULL";
    $db_results = Dba::read($sql,array($site,$field));

    $row = Dba::fetch_assoc($db_results);
    
    $value = isset($row['value']) ? $row['value'] : NULL;

    return $value;

  } // get_data

  /**
   * set_data
   */
  public static function set_data($site,$field,$value) { 

    $allowed_fields = array('project','accession');

    if (!in_array($field,$allowed_fields)) {
      return false;
    }

    // Set Closed date on any existing
    $sql = "UPDATE `site_data` SET `closed`=? WHERE `site`=? AND `key`=? AND `closed` IS NULL";
    $db_results = Dba::write($sql,array(time(),$site,$field));

    // Set New value
    $sql = "INSERT INTO `site_data` (`site`,`key`,`value`,`created`) VALUES (?,?,?,?)";
    $db_results = Dba::write($sql,array($site,$field,$value,time()));

    return $db_results;

  } // set_data
  
  /**
   * create
   */
  public static function create($input) { 

    // Clear any previous Error state
    Error::clear(); 

    if (!Site::validate($input)) {
      Error::add('general','Invalid Field Values - please check input');
      return false; 
    }

    $name       = Dba::escape($input['name']);
    $desc       = Dba::escape($input['description']);
    $exc_start  = strlen($input['excavation_start']) ? $input['excavation_start'] : NULL;
    $exc_end    = strlen($input['excavation_end']) ? $input['excavation_end'] : NULL;
    $pi = Dba::escape($input['pi']);
    $elevation = Dba::escape($input['elevation']);
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $partners = Dba::escape($input['partners']);
    $sql = "INSERT INTO `site` (`name`,`description`,`principal_investigator`,`excavation_start`,`excavation_end`,`partners`,`northing`,`easting`,`elevation`,`enabled`) " . 
      "VALUES ('$name','$desc','$pi',?,?,'$partners','$northing','$easting','$elevation','1')";
    $results = Dba::write($sql,array($exc_start,$exc_end)); 

    $insert_id = Dba::insert_id();

    if (!$insert_id OR !$results) { 
      Error::add('general','Unknown database error adding new site');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * update
   * Updates a site
   */
  public function update($input) { 

    // Reset the error state
    Error::clear();

    if (!Site::validate($input,$this->uid)) { 
      Error::add('general','Invalid Field Values - Please check your input and try again');
      return false;
    }

    $uid = Dba::escape($this->uid);
    $name = Dba::escape($input['name']);
    $pi = Dba::escape($input['pi']);
    $description = Dba::escape($input['description']);
    $partners = Dba::escape($input['partners']);
    $exc_start = Dba::escape($input['excavation_start']);
    $exc_end = Dba::escape($input['excavation-end']);
    $elevation = Dba::escape($input['elevation']);
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $sql = "UPDATE `site` SET `name`='$name', `principal_investigator`='$pi',`description`='$description'," . 
      "`partners`='$partners',`excavation_start`='$exc_start',`excavation_end`='$exc_end',`elevation`='$elevation'," . 
      "`northing`='$northing',`easting`='$easting' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    if (!$db_results) { 
      Error::add('general','Unknown Database Error - Please try again');
      return false;
    }

    return true;

  } // update

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input,$uid=0) { 

    // Make sure there's a name and it's unique
    if (!Field::notempty($input['name'])) { 
      Error::add('name','Required Field');
    }

    // Site must be Alphanumeric
    if (!Field::validforfilename($input['name'])) {
      Error::add('name','Name must contain only [-,_,A-Z,a-z,0-9]');
    }

    $site_uid = Site::get_from_name($input['name']);

    if ($site_uid > 0 AND $site_uid != $uid) {
      Error::add('name','Name already exists');
    }

    // Require a start a PI
    if (!Field::notempty($input['pi'])) {
      Error::add('pi','Required Field');
    } 


    // Make sure if start and end are set that end is after start
    $start = strtotime($input['excavation_start']);
    $end = strtotime($input['excavation_end']);

    if ($start > 0 AND $end > 0 AND $start > $end) { 
      Error::add('excavation_end','End must be after Start');
    }

    if (Error::occurred()) { return false; }

    return true; 

  } // validate

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
