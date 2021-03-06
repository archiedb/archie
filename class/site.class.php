<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Site extends database_object { 

  public $uid; 
  public $name;
  public $settings; // JSON encoded settings, includes fields[] array 
  public $description;
  public $northing; 
  public $easting;
  public $elevation;
  public $principal_investigator; // site.principal_investigator
  public $partners; // text field
  public $excavation_start; // timestamp
  public $excavation_end; // timestamp
  public $enabled; 

  // Current allowed settings
  private $allowed_settings = array('catalog_offset'=>'Catalog Start','lus'=>'L.U','units'=>'Unit','quads'=>'Quad','ticket'=>'Ticket Format');

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

    $this->excavation_end_date = empty($this->excavation_end) ? '' : date('d-M-Y',$this->excavation_end);
    $this->excavation_start_date = empty($this->excavation_start) ? '' : date('d-M-Y',$this->excavation_start);

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
   * add_field
   * This adds a custom field to the site, for now it's only for records
   */
  public function add_field($input) { 

    // FIXME: Allow this to change
    $input['type'] = 'record';

    // Validate the field
    if (!$this->validate_field($input)) {
      Err::add('general','Invalid field specified');
      return false;
    }

    // Spaces are the devil
    $input['fieldname'] = str_replace(' ','_',$input['fieldname']);

    // Add this key to the existing ones
    $fields = $this->get_setting('fields');
    $fielduid = $input['type'] . $input['fieldname'];

    // Make sure there's no overlap
    if (isset($fields[$fielduid])) {
      Err::add('general','Duplicate Field, unable to add');
      return false;
    }

    $fields[$fielduid] = array('object'=>$input['type'],
                          'name'=>$input['fieldname'],
                          'type'=>$input['fieldtype'],
                          'validation'=>$input['fieldvalidation'],
                          'enabled'=>$input['enabled']);
    $this->update_settings(array('key'=>'field','fields'=>$fields));

    return true;

  } // add_field

  /**
   * disable_field
   * Disable a custom field
   */
  public function disable_field($fielduid) { 

    $fields = $this->get_setting('fields');

    // Disable the fielduid if it exists
    if (isset($fields[$fielduid])) {
      $fields[$fielduid]['enabled'] = 0;
    }
    
    $this->update_settings(array('key'=>'field','fields'=>$fields));

    return true; 

  } // disable_field

  /**
   * enable_field
   * Enable a custom field
   */
  public function enable_field($fielduid) { 

      $fields = $this->get_setting('fields');

      // Enable the fielduid if it exists
      if (isset($fields[$fielduid])) {
        $fields[$fielduid]['enabled'] = 1;
      }

      $this->update_settings(array('key'=>'field','fields'=>$fields));

      return true; 

  } // enable_field

  /**
   * decode_settings
   * This takes the ->settings field from the DB
   * runs a json_decode() and does what it needs to
   */
  public function decode_settings() { 

    $settings = json_decode($this->settings,true); 

    // Check for some defaults, load from dist file if none set
    if (!isset($settings['catalog_offset'])) {
      $settings['catalog_offset'] = '0';
    }
    if (!isset($settings['units'])) { 
      $settings['units'] = fgetcsv(fopen(Config::get('prefix') . '/config/units.csv.dist','r'));
    }
    if (!isset($settings['quads'])) {
      $settings['quads'] = fgetcsv(fopen(Config::get('prefix') . '/config/quads.csv.dist','r'));
    }
    if (!isset($settings['ticket'])) { 
      $settings['ticket'] = Config::get('ticket_size');
    }
    if (!isset($settings['lus'])) {
      $settings['lus'] = fgetcsv(fopen(Config::get('prefix') . '/config/lus.csv.dist','r'));
    }
    if (!isset($settings['fields'])) { 
      $settings['fields'] = array();
    }

    // Re-assign
    $this->settings = $settings;

    return true; 

  } // decode_settings

  /**
   * update_settings
   * json_encode() and update db
   */
  public function update_settings($input) { 

    // Validate settings
    if (!$this->validate_settings($input)) {
      Err::add('general','Invalid settings specified');
      return false;
    }

    // Setup the new array
    $settings = array();
    $settings['catalog_offset'] = isset($input['catalog_offset']) ? intval($input['catalog_offset']) : $this->get_setting('catalog_offset');
    $settings['quads']  = isset($input['quads']) ? explode(',',$input['quads']) : $this->get_setting('quads'); 
    $settings['units']  = isset($input['units']) ? explode(',',$input['units']) : $this->get_setting('units'); 
    $settings['ticket'] = isset($input['ticket']) ? $input['ticket'] : $this->get_setting('ticket'); 
    $settings['lus']    = isset($input['lus']) ? explode(',',$input['lus']) : $this->get_setting('lus');
    $settings['fields'] = isset($input['fields']) ? $input['fields'] : $this->get_setting('fields');

    $sql = "UPDATE `site` SET `settings`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array(json_encode($settings),$this->uid));

    return true;

  } // update_settings

  /**
   * validate_field
   * Validate a new custom field
   */
  public function validate_field($input) {

    $retval = true;

    // Make sure we're not using a reserved name for the record type
    switch ($input['type']) {
      default:
      case 'record':
        $sql = "DESCRIBE `record`";
        $db_results = Dba::read($sql);
        while ($row = Dba::fetch_assoc($db_results)) { 
          if ($row['Field'] == $input['fieldname']) {
            $retval = false; 
            Err::add('general','Field names must be unique');
          }
        }

      break;
    }

    if (strlen($input['fieldname']) > 18) {
      Err::add('general','Field names must be less than 18 characters'); 
      $retval = false;
    }

    // Name must be A-Z0,9
    if (!preg_match('/[a-zA-Z0-9 ]/',$input['fieldname'])) {
      Err::add('general','Field name must be A-Z,0-9 and spaces only');
      $retval = false;
    }

    // Type must be short, text, boolean
    if (!in_array($input['fieldtype'],array('string','text','boolean'))) {
      Err::add('general','Invalid Field type, please try again');
      $retval = false;
    }

    // Validation must be words whole numbers decimal or boolean
    if (!in_array($input['fieldvalidation'],array('string','integer','decimal','boolean'))) {
      Err::add('general','Invalid Validation method, please try again');
      $retval = false;
    }
    // Enabled must be true/false
    if ($input['enabled'] != 0 AND $input['enabled'] != 1) {
      Err::add('general','Invalid Field state, please try again');
      $retval = false;
    }

    return $retval; 

  } // validate_field

  /**
   * validate_settings
   * validate the settings
   */
  public function validate_settings($input) { 

    switch ($input['key']) {
      case 'field':
        return true;
      break;
      case 'catalog_offset':
        // Just needs to be a positive number
        if ($input['catalog_offset'] < 0 OR intval($input['catalog_offset']) != $input['catalog_offset']) {
          Err::add('general','Catalog Offset must be a postive whole number');
          return false;
        }
        return true;
      break;
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
        if (!$retval) { Err::add('units','Invalid Units, only A-Z,0-9,_,- allowed, Invalid Unit(s) - ' . $invalid_units); }
        return $retval; 
      break;
      case 'lus':
        $invalid_lus = '';
        $retval = true;
        $lus = explode(',',$input['lus']);
        foreach ($lus as $lu) {
          if (preg_match('/[^a-z_\-0-9]/i',$lu)) {
            $retval = false;
            $invalid_lus .= $lu . ' :: ';
          }
        }
        if (!$retval) { Err::add('lus','Invalid LUs - ' . $invalid_lus); }
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
            $invalid_quads .= $quad . ' :: ';
          }
        } 
        if (!$retval) { Err::add('quads','Invalid Quads, only A-Z,0-9,_,- allowed, Invalid Quad(s) - '. $invalid_quads); }
        return $retval;
      break;
    }

    return false;

  } // validate_settings

  /**
   * get_valid_settings
   * returns a key'd array of valid settings
   */
  public function get_valid_settings() { 

    return $this->allowed_settings;

  } // get_valid_settings

  /**
   * get_setting
   * Return a setting from this site of the name specified
   */
  public function get_setting($name) { 

      if (isset($this->settings[$name])) {
        return $this->settings[$name];
      }
      else {
        Event::error('Site::get_setting','Invalid Setting ' . $name . ' requested');
        return false;
      }

  } // get_setting

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
   * _display
   * Show the pretty version of things
   */
  public function _display($variable) { 


  } // _display

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
    Err::clear(); 

    if (!Site::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false; 
    }

    $exc_start    = empty($input['excavation_start']) ? NULL : strtotime($input['excavation_start']);
    $exc_end      = empty($input['excavation_end']) ? NULL : strtotime($input['excavation_end']);
    $description  = empty($input['description']) ? NULL : $input['description'];
    $northing     = empty($input['northing']) ? NULL : $input['northing'];
    $easting      = empty($input['easting']) ? NULL : $input['easting'];
    $elevation    = empty($input['elevation']) ? NULL : $input['elevation'];
    $partners     = empty($input['partners']) ? NULL : $input['partners'];
    $sql = "INSERT INTO `site` (`name`,`description`,`principal_investigator`,`excavation_start`,`excavation_end`,`partners`,`northing`,`easting`,`elevation`,`enabled`) " . 
      "VALUES (?,?,?,?,?,?,?,?,?,?)";
    $results = Dba::write($sql,array($input['name'],$description,$input['pi'],$exc_start,$exc_end,$partners,$northing,$easting,$elevation,1)); 

    if (!$results) { 
      Err::add('general','Unable to add site, please see error log');
      return false;
    }

    $insert_id = Dba::insert_id();

    $json_log = json_encode(array('Name'=>$input['name'],'Description'=>$input['description'],'PI'=>$input['pi'],'Exc Start'=>$input['excavation_start'],
      'Exc End'=>$input['excavation_end'],'Partners'=>$input['partners'],'Northing'=>$input['northing'],'Easting'=>$input['easting'],'Elevation'=>$input['elevation'],
      'Enabled'=>1));
    Event::record('site::create',$json_log);

    return $insert_id;

  } // create

  /**
   * update
   * Updates a site
   */
  public function update($input) { 

    // Reset the error state
    Err::clear();

    if (!Site::validate($input,$this->uid)) { 
      Err::add('general','Invalid Field Values - Please check your input and try again');
      return false;
    }

    $description  = empty($input['description']) ? NULL : $input['description'];
    $partners     = empty($input['partners']) ? NULL : $input['partners'];
    $exc_start    = empty($input['excavation_start']) ? NULL : strtotime($input['excavation_start']);
    $exc_end      = empty($input['excavation_end']) ? NULL : strtotime($input['excavation_end']);
    $elevation    = empty($input['elevation']) ? NULL : $input['elevation'];
    $northing     = empty($input['northing']) ? NULL : $input['northing'];
    $easting      = empty($input['easting']) ? NULL : $input['easting'];
    $sql = "UPDATE `site` SET `name`=?, `principal_investigator`=?,`description`=?," . 
      "`partners`=?,`excavation_start`=?,`excavation_end`=?,`elevation`=?," . 
      "`northing`=?,`easting`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array($input['name'],$input['pi'],$description,$partners,$exc_start,$exc_end,$elevation,$northing,$easting,$this->uid));

    if (!$db_results) { 
      Err::add('general','Unable to update View, please check error log.');
      return false;
    }

    $json_log = json_encode(array('Name'=>$input['name'],'Description'=>$input['description'],'PI'=>$input['pi'],'Exc Start'=>$input['excavation_start'],
      'Exc End'=>$input['excavation_end'],'Partners'=>$input['partners'],'Northing'=>$input['northing'],'Easting'=>$input['easting'],'Elevation'=>$input['elevation'],
      'Enabled'=>1));
    Event::record('site::update',$json_log);

    return true;

  } // update

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input,$uid=0) { 

    // Make sure there's a name and it's unique
    if (!Field::notempty($input['name'])) { 
      Err::add('name','Required Field');
    }

    // Site must be Alphanumeric
    if (!Field::validforfilename($input['name'])) {
      Err::add('name','Name must contain only [-,_,A-Z,a-z,0-9]');
    }

    $site_uid = Site::get_from_name($input['name']);

    if ($site_uid > 0 AND $site_uid != $uid) {
      Err::add('name','Name already exists');
    }

    // Require a start a PI
    if (!Field::notempty($input['pi'])) {
      Err::add('pi','Required Field');
    } 

    // Make sure northing/easting/elevation are numeric
    if (!Field::validate('elevation',$input['elevation'])) {
      Err::add('elevation','Invalid Elevation');
    }

    if (!Field::validate('northing',$input['northing'])) {
      Err::add('northing','Invalid Northing');
    }

    if (!Field::validate('easting',$input['easting'])) {
      Err::add('easting','Invalid Easting');
    }

    // Make sure if start and end are set that end is after start
    $start = strtotime($input['excavation_start']);
    $end = strtotime($input['excavation_end']);

    if ($start > 0 AND $end > 0 AND $start > $end) { 
      Err::add('excavation_end','End must be after Start');
    }

    if (strlen($input['excavation_start']) AND !$start) {
      Err::add('excavation_start','Invalid Date specified');
    }

    if (strlen($input['excavation_end']) AND !$end) {
      Err::add('excavation_end','Invalid Date format specified');
    }

    if (Err::occurred()) { return false; }

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
