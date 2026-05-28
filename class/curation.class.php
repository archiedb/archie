<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Curation extends database_object { 

	public $uid; 
  public $site; // FK_Site
  public $catalog_id; // If assoicated with a Record
  public $instituation; // FK Institution
  public $building; // FK Building
  public $room; // FK Room
  public $cabinet; // FK Cabinet
  public $drawer; // FK Drawer
  public $status; // Status of artifact (Loaned etc)
  public $image; // Primary Image FK
  public $created;
  public $created_by; // FK User
  public $updated;
  public $updated_by; // FK User

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid) OR !$uid) { return false; } 

		$row = $this->get_info($uid,'curation'); 

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

    $this->record = 'CUR-' . $this->uid;
    $this->site = new Site($this->site);

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

    $sql = 'SELECT * FROM `curation` WHERE `curation`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('curation',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		parent::remove_from_cache('curation',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * _display
   * Display friendly stuff
   */
  public function _display($variable) { 



  } // _display

  /**
   * update
   * Update an existing curation, this is only related to the description and keywords
   * this doesn't deal with the location/media information
   */
  public function update($input) {

    Err::clear();

    if (!Curation::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    $updated      = date('Y-m-d h:i:s',time());
    $updated_by   = \UI\sess::$user->uid;
    $sql = "UPDATE `curation` SET `updated`=?, `updated_by`=?, `catalog_id`=?, `institution`=?, `building`=?, " . 
      "`room`=?, `cabinet`=?, `drawer`=?, `notes`=? , `status`=? WHERE `uid`=?";
    $db_results = Dba::write($sql,array($updated,$updated_by,$input['catalog_id'],$input['institution'],$input['building'],
      $input['room'],$input['cabinet'],$input['drawer'],$input['notes'],$input['status'],$uid)); 

    if (!$db_results) { 
      Err::add('general','Unable to update Curation - please see error log');
      return false;
    }

    $institution  = new Institution($input['institution']);
    $building     = new Building($input['building']);
    $room         = new Room($input['room']);
    $cabinet      = new Cabinet($input['cabinet']);
    $drawer       = new Drawer($input['drawer']);

    $log_json = json_encode(array('Curation'=>'#' . $this->uid,'User'=>\UI\sess::$user->username,
      'Catalog ID'  =>$input['catalog_id'],
      'Notes'       =>$input['notes'],
      'Institution' =>'#' . $institution->uid . ' ' . $institution->name,
      'Building'    =>'#' . $building->uid . ' ' . $building->name,
      'Room'        =>'#' . $room->uid . ' ' . $room->name,
      'Cabinet'     =>'#' . $cabinet->uid . ' ' . $cabinet->name,
      'Drawer'      =>'#' . $drawer->uid . ' ' . $drawer->name));
    Event::record('curation::update',$log_json);

    $this->refresh();

    return true;

  } // update

  /**
   * create
   * Create a new Feature, this also has to insert the initial spatial location
   */
  public static function create($input) { 

    Err::clear();

    // Force the site to the current users site
    $input['site'] = \UI\sess::$user->site->uid;

    if (!Curation::validate($input)) {
      Err::add('general','Invalid Field Values - please check input');
      return false;
    }

    // Start the transaction
    if (!Dba::begin_transaction()) { 
      Err::add('general','Unable to start DB Transaction, please try again');
      return false; 
    }

    // Check for duplicate records
    if (strlen($input['catalog_id'])) { 
      $curation = \Curation::get_from_catalog_id($input['catalog_id'],$input['site']); 
      if ($curation->id) {
        Err::add('general','Duplicate Curation #' . $curation->id . ' found for Catalog ID #' . \UI\htmlout($input['catalog_id']));
        return false; 
      }
    }
    // Attempt to find a curation based on everything _BUT_ the catalog ID
    $curation = \Curation::get_from_location($input);
    if ($curation->id) { 
      Err::add('general','Duplicate Curation #' . $curation->id . ' found in the same location');
      return false; 
    }

    $input['created'] = date('m-d-Y h:i:s');
    $input['created_by'] = \UI\sess::$user->uid;

    $dbvalues = array(
      $input['site'],
      $input['catalog_id'],
      $input['notes'],
      $input['institution'],
      $input['building'],
      $input['room'],
      $input['cabinet'],
      $input['drawer'],
      $input['created'],
      $input['created_by']);

    $sql = "INSERT INTO `curation` (`site`,`catalog_id`,`notes`,`institution`,`building`,`room`,`cabinet`,`drawer`,`created`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $db_results = Dba::write($sql,$dbvalues);

    if (!$db_results) { 
      Error:add('general','Unknown Error - inserting curation into database');
      $retval = Dba::rollback();
      if (!$retval) { Err::add('general','Unable to roll database changes back, please report this to your Administrator'); }
      Dba::commit();
      return false;
    }

    // Take the insert_id and return it
    $insert_id = Dba::insert_id();
    
    $log_json = json_encode(array('Site'=>$input['site'],'Catalog ID'=>$input['catalog_id'],'Notes'=>$input['notes'],
        'Institution'=>$institution->name,'Building'=>$building->name,'Room'=>$room->name,'Cabinet'=>$cabinet->name,'Drawer'=>$drawer->name,'User'=>\UI\sess::$user->username,'Created'=>date("r",$created)));

    Event::record('curation::create',$log_json);
    
    if (!Dba::commit()) { 
      Event::record('DBA::commit','Commit Failure - unable to close transaction');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    // Check to make sure the locations are valid for this site
    $building = new \Building($input['building']);
    if (!$building->uid) {
      Err::add('building','Unable to find building');
    }
    else {
      if (!$building->institution != $input['institution']) {
        Err::add('building','Building not at this institution');
      }
      if (!$building->is_enabled()) {
        Err::add('building','Building is not enabled');
      }
    } // End Building found

    $institution = new \Institution($input['institution']);
    if (!$institution->uid) {
      Err::add('institution','Unable to find institution');
    }
    else {
      if (!$institution->is_enabled()) {
        Err::add('institution','Institution not enabled');
      }
    } // End Institution exists

    $room = new Room($input['room']);
    if (!$room->uid) {
      Err::add('room','Unable to find room');
    }
    else {
      if ($room->building != $input['building']) {
        Err::add('room','Room not in the specified building');
      }
      if (!$room->is_enabled()) {
        Err::add('room','Room is not enabled');
      }
    } // End if Room found

    $cabinet = new Cabinet($input['cabinet']);
    if (!$cabinet->uid) {
      Err::add('cabinet','Unable to find cabinet');
    }
    else {
      if ($cabinet->room != $input['room']) {
        Err::add('cabinet','Cabinet not in specified room');
      }
      if (!$cabinet->is_enabled()) { 
        Err::add('cabinet','Cabinet is not enabled');
      }
    } // End if cabinet found

    if (Err::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * delete
   * Delete the curation record
   */
  public function delete () { 

    $sql = "DELETE FROM `curation` WHERE `uid`=?";
    $db_results = Dba::write($sql,array($this->uid));

    return true;

  } // delete

  /**
   * last_created
   * Returns the most recently created curation, so people can remember what they just did
   */
  public static function last_created() {


  } // last_created

  /**
   * get_institutions
   * Return an array of institution Id's assoicated with the current site
   */
  public static function get_institutions($all=false) {

    $results = array();


    return $results; 

  } // get_institutions

  /**
   * get_buildings
   * Return an array of building IDs assoicated with the current site
   */
  public static function get_buildings($institution_id) {

    $results = array();


    return $results; 

  } // get_buildings

  /**
   * get_rooms
   * Return an array of rooms assoicated with the current site
   */
  public static function get_rooms($building_id) { 

    $results = array();


    return $results; 

  } // get_rooms

  /**
   * get_cabinets
   * Return an array of cabinets assoicated with the current room
   */
  public static function get_cabinets($room_id) {

    $resuilts = array();

    return $results; 

  } // get_cabinets

  /**
   * get_drawers
   * Return an array of drwaers assoicated with the current cabinet
   */
  public static function get_drawers($cabinet_id) {

    $results = array();


    return $results; 

  } // get_drawers

  /**
   * is_on_loan
   * Returns true if it's been loaned out
   */
  public function is_on_loan() {

    if ($this->status == 'loan') { return true; }

    return false;

  } // is_on_loan

  /**
   * get_user_curation
   * Returns the curation assoicated with this user
  */
  public static function get_user_curation($uid=false,$limit=3) { 

    if (!$uid) {
      $uid = \UI\sess::$user->uid;
    }

    $results = array();

    $uid = Dba::escape($uid);
    $limit = abs(floor($limit));
    $sql = "SELECT * FROM `curation` WHERE `user`='$uid' AND `site`=? ORDER BY `created` DESC LIMIT $limit";
    $db_results = Dba::read($sql,array(\UI\sess::$user->site->uid));

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid'];
      parent::add_to_cache('curation',$row['uid'],$row);
    }

    return $results;

  } // get_user_krotovina

} // end class level
?>
