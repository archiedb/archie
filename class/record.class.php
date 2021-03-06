<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
class Record extends database_object { 

  public $uid; // INTERNAL 
  public $site; // Site UID  
  public $catalog_id; // # of item unique to site
  public $inventory_id; // this is the built ID of the thingy from site + year + catalog id
  public $feature; // Optional
  public $krotovina; // Optional
  public $level; // Optional 
  public $lsg_unit; 
  public $station_index; // LISTED AS RN in the interface (>.<)
  public $xrf_matrix_index; 
  public $weight; 
  public $width; 
  public $height; // Displayed as Length in interface (>.<)
  public $thickness; 
  public $quanity; 
  public $material; // FK
  public $classification; // FK
  public $xrf_artifact_index; 
  public $accession;
  public $notes; 
  public $user_id; // The ID
  public $northing; // Northing from station info
  public $easting; // Easting from station info
  public $elevation; // Elevation from station info
  public $user; // The Object 
  public $created; 
  public $updated; 

  public $extra; // Additional custom field information (JSON encoded in DB, decoded in object) not directly accessible

	// Constructor
  public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 
		
		$row = $this->get_info($uid,'record'); 
    if (!is_array($row)) { return false; }

		foreach ($row as $key=>$value) { $this->$key = $value; } 

		// Setup the Material and classification
    $spatial = SpatialData::get_record_data($this->uid,'record','single');
    // don't load this if it isn't there
    if (is_object($spatial)) {
      $this->northing = $spatial->northing;
      $this->easting = $spatial->easting;
      $this->elevation = $spatial->elevation;
      $this->station_index = $spatial->station_index;
    }
		$this->material = new Material($this->material); 
		$this->classification = new Classification($this->classification); 
		$this->lsg_unit	= new lsgunit($this->lsg_unit); 
    $this->site = new site($this->site);
		$this->inventory_id = $this->site->name . '.' . date('Y',$this->created) . '-' . $this->catalog_id;
    $this->record = $this->site->name . '-' . $this->catalog_id;
		$this->user_id = $this->user; 
    $this->feature = new Feature($this->feature);
    $this->krotovina = new Krotovina($this->krotovina);
    $this->level = new Level($this->level);
		$this->user = new User($this->user); 
    $this->extra = json_decode($this->extra,true); // Decode and reassign extra JSON crap

		return true; 

	} // constructor

	/**
	 * build_cache
	 * Take an array of IDs and cache them (avoiding 1000 queries and do one
	 */
	public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; } 

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false) causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `record` WHERE `record`.`uid` IN ' . $idlist;
    $db_results = Dba::read($sql); 

    $materials = array(); 
    $classifications = array(); 
    $users = array(); 
    $records = array();

    while ($row = Dba::Fetch_assoc($db_results)) { 
      parent::add_to_cache('record',$row['uid'],$row);
      $materials[$row['material']] = $row['material']; 
      $classifications[$row['classification']] = $row['classification']; 
      $users[$row['user']] = $row['user']; 
      $records[] = $row['uid'];
    } 

    // Cache the spatial data
    SpatialData::build_cache($records,'record');
    
    Material::build_cache($materials); 
    User::build_cache($users); 
    Classification::build_cache($classifications); 

    return true; 

	} // build_cache

	/**
	 * refresh
	 * Refreshes this object from the database, clears cache
	 */
	public function refresh() { 

		Record::remove_from_cache('record',$this->uid); 
		$retval = $this->__construct($this->uid); 


	} // refresh

  /** 
   * _display
   * Show the pretty version of things
   */
  public function _display($variable) { 


  } // _display

	// Create
	public static function create($input) { 

    // Clear any previous errors before we do the validatation
    Err::clear(); 

    // Set the site based on the session users current site
    $input['site'] = \UI\sess::$user->site->uid;

		// First verify the input to make sure
		// all of the fields are within acceptable tolerences 
		if (!Record::validate($input)) { 
			Err::add('general','Invalid Field Values - please check input'); 
			return false; 
		} 

    if (!Dba::begin_transaction()) {
      Err::add('general','Unable to start DB Transaction, please try again');
      return false; 
    }

		// Reset Row variable
		$row = array(); 

    // If catalog_id isn't set, set it to null
    $input['catalog_id'] = isset($input['catalog_id']) ? $input['catalog_id'] : null;

		// If no catalog_id specified, determine next available #
		if (!$input['catalog_id']) { 
			$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`=? ORDER BY `catalog_id` DESC LIMIT 1 FOR UPDATE"; 
			$db_results = Dba::read($catalog_sql,array($input['site'])); 
      if (!$db_results) {
        Err::add('general','Database timeout reached, please re-submit'); 
        return false;
      }
			$row = Dba::fetch_assoc($db_results); 	
			$catalog_id = $row['catalog_id']+1; 
		} 

    // Make sure it's bigger then the catalog_offset site setting
    if ($catalog_id < \UI\sess::$user->site->settings['catalog_offset']) {
      $catalog_id = \UI\sess::$user->site->settings['catalog_offset'];
    }

    // No matter what make sure this isn't a duplicate
		$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`=? AND `catalog_id`=? LIMIT 1 FOR UPDATE"; 
		$db_results = Dba::read($catalog_sql,array($input['site'],$input['catalog_id'])); 
    if (!$db_results) {
      Err::add('general','Database timeout reached, please re-submit'); 
      return false;
    }
		$row = Dba::fetch_assoc($db_results); 
		if ($row['catalog_id']) { 
			Err::add('general','Database Failure - Duplicate CatalogID - ' . $catalog_id); 
      Dba::commit();
			return false; 
		} 

    // We need the real UID of the following objects
    $level = $input['level'] ? new Level($input['level']) : NULL;

    $feature_uid    = empty($input['feature']) ? NULL : Feature::get_uid_from_record($input['feature']);
    $krotovina_uid  = empty($input['krotovina']) ? NULL : Krotovina::get_uid_from_record($input['krotovina']);

		// Normalize the input, set unset variables to NULL
    $site               = \UI\sess::$user->site->uid;
		$level              = strlen($input['level']) ? $level->uid : NULL;
		$lsg_unit           = $input['lsg_unit']; 
		$xrf_matrix_index   = empty($input['xrf_matrix_index']) ? NULL : $input['xrf_matrix_index'];
		$xrf_artifact_index = empty($input['xrf_artifact_index']) ? NULL : $input['xrf_artifact_index'];
		$weight             = empty($input['weight']) ? NULL : $input['weight'];
		$height             = empty($input['height']) ? NULL : $input['height'];
		$width              = empty($input['width']) ? NULL : $input['width'];
		$thickness          = empty($input['thickness']) ? NULL : $input['thickness'];
		$quanity            = empty($input['quanity']) ? '1' : $input['quanity']; // Default to Quanity 1 
		$material           = $input['material']; 
		$classification     = $input['classification']; 
		$notes              = empty($input['notes']) ? NULL : $input['notes'];
    $accession          = empty(\UI\sess::$user->site->accession) ? NULL: \UI\sess::$user->site->accession;
    $station_index      = empty($input['station_index']) ? NULL : $input['station_index'];
    $northing           = empty($input['northing']) ? NULL : $input['northing'];
    $easting            = empty($input['easting']) ? NULL : $input['easting'];
    $elevation          = empty($input['elevation']) ? NULL : $input['elevation'];
		$feature            = $feature_uid;  
    $krotovina          = $krotovina_uid;
		$user               = \UI\sess::$user->uid; 
		$created            = time(); 

		$sql = "INSERT INTO `record` (`site`,`catalog_id`,`level`,`lsg_unit`,`xrf_matrix_index`,`weight`,`height`,`width`,`thickness`,`quanity`,`material`,`classification`,`notes`,`xrf_artifact_index`,`accession`,`feature`,`krotovina`,`user`,`created`) " . 
			"VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
		$db_results = Dba::write($sql,array($site,$catalog_id,$level,$lsg_unit,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$notes,$xrf_artifact_index,$accession,$feature,$krotovina,$user,$created)); 

		if (!$db_results) { 
			Err::add('general','Unable to insert record, reverting changes.'); 
      // Roll the transaction back
      $retval = Dba::rollback();
      if (!$retval) { Err::add('general','Unable to roll Database changes back, please report this to your Administrator'); }
      Dba::commit();
			return false; 
		} 

		$insert_id = Dba::insert_id();
    $log_json = json_encode(array('Site'=>$site,'Catalog ID'=>$catalog_id,'Level'=>$input['level'],'LSG Unit'=>$lsg_unit,
                  'StationIndex'=>$station_index,'XRFMatrixIndex'=>$xrf_matrix_index,'Weight'=>$weight,
                  'Height'=>$height,'Thickness'=>$thickness,'Quanity',$quanity,'Material'=>$material,
                  'Classification'=>$classification,'Feature ID'=>$feature_uid,'Krotovina ID'=>$krotovina_uid,
                  'Notes'=>$notes,'Accession'=>$accession,'User'=>\UI\sess::$user->username,'Date'=>date("r",$created)));
		Event::record('record::create',$log_json); 

    // Create the spatial data entry
    if ($station_index OR $northing OR $easting OR $elevation) { 
      $spatial = SpatialData::create(array('station_index'=>$station_index,
                                        'record'=>$insert_id,
                                        'northing'=>$northing,
                                        'easting'=>$easting,
                                        'elevation'=>$elevation,
                                        'type'=>'record'));
    } // end if they specified something spatial

		// We're sure we've got a record so lets generate our QR code. 
		Content::write($insert_id,'qrcode'); 

    // Commit and unlock
    if (!Dba::commit()) {
      Event::record('DBA::commit','Commit Failure - unable to close transaction');
      return false;
    }

		return $insert_id; 

	} // create	

	// Update
	public function update($input) { 

    // Clear any previous errors before we do the validatation
    Err::clear(); 

    // Set the site, they can't change this
    $input['site'] = $this->site->uid;
    // Load existing fields
    $extra = $this->extra;

    // Foreach the Site custom fields, and look for them in $_POST
    $site_fields = $this->site->get_setting('fields');
    $retval = true;
    foreach ($site_fields as $field) { 
      if (isset($_POST[$field['name']])) {
        // If it's blank no error checking
        if (empty($_POST[$field['name']])) { 
          $extra[$field['name']] = $_POST[$field['name']];
          continue; 
        }
        // Do the validation 
        switch ($field['validation']) {
          case 'boolean':
            if ($_POST[$field['name']] != '0' AND $_POST[$field['name']] != '1') {
              Err::add($field['name'],'Must be True or False');
              $retval = false;
            }
          break;
          case 'integer':
            if (floor($_POST[$field['name']]) != $_POST[$field['name']] OR preg_match('/[^0-9]/',$_POST[$field['name']])) {
              Err::add($field['name'],'Must be a whole number with no decimals');
              $retval = false;
            }
          break;
          case 'decimal':
            if (floatval($_POST[$field['name']]) != $_POST[$field['name']] OR !preg_match('/^[0-9](\.[0-9]+)?$/',$_POST[$field['name']])) {
              Err::add($field['name'],'Must be a number');
              $retval = false; 
            }
          break;
          default:
          case 'string':
            // Don't care...
          break;
        }
        $extra[$field['name']] = $_POST[$field['name']];
      }
    } // end foreach custom fields

  
    // First verify the input to make sure
    // all of the fields are within acceptable tolerences 
    if (!Record::validate($input,$this->uid)) {
      Err::add('general','Invalid Field Values - Please check your input again');
      return false;
    }

    // FIXME: integrate this into validate
    if ($retval == false) { return false; }


    // We need the real UID of the following objects
    $level              = strlen($input['level']) ? new Level($input['level']) : NULL;
    $level_uid          = strlen($input['level']) ? $level->uid : NULL;
    $feature_uid        = empty($input['feature']) ? NULL : Feature::get_uid_from_record($input['feature']);
    $krotovina_uid      = empty($input['krotovina']) ? NULL : Krotovina::get_uid_from_record($input['krotovina']);

		$lsg_unit           = $input['lsg_unit']; 
		$xrf_matrix_index   = strlen($input['xrf_matrix_index']) ? $input['xrf_matrix_index'] : NULL; 
		$weight             = strlen($input['weight']) ? $input['weight'] : NULL;
		$height             = strlen($input['height']) ? $input['height'] : NULL;
		$width              = strlen($input['width']) ? $input['width'] : NULL; 
		$thickness          = strlen($input['thickness']) ? $input['thickness'] : NULL;
		$quanity            = strlen($input['quanity']) ? $input['quanity'] : '1';
		$material           = $input['material']; 
		$classification     = $input['classification']; 
		$notes              = $input['notes']; 
		$xrf_artifact_index = strlen($input['xrf_artifact_index']) ? $input['xrf_artifact_index'] : NULL;
		$feature            = ($feature_uid > 0) ? $feature_uid : NULL;
    $krotovina          = ($krotovina_uid > 0) ? $krotovina_uid : NULL;
    $northing           = isset($input['northing']) ? $input['northing'] : $this->northing; 
    $easting            = isset($input['easting']) ? $input['easting'] : $this->easting; 
    $elevation          = isset($input['elevation']) ? $input['elevation'] : $this->elevation; 
		$user               = \UI\sess::$user->uid; 
		$updated            = time(); 
		$record_uid         = $this->uid; 
		$station_index      = isset($input['station_index']) ? $input['station_index'] : NULL;
    $extra              = count($extra) ? json_encode($extra) : NULL;

		$sql = "UPDATE `record` SET `level`=?, `lsg_unit`=?, `xrf_matrix_index`=?, `weight`=?, `height`=?, " . 
      "`width`=?, `thickness`=?, `quanity`=?, `material`=?, `classification`=?, `notes`=?, `xrf_artifact_index`=?, " . 
			"`user`=?, `updated`=?, `feature`=?, `krotovina`=?, `extra`=? WHERE `uid`=?"; 
		$db_results = Dba::write($sql,array($level_uid,$lsg_unit,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$notes,$xrf_artifact_index,$user,$updated,$feature,$krotovina,$extra,$record_uid)); 

		if (!$db_results) { 
			Err::add('general','Database Error, please try again'); 
			return false; 
		} 

    // Before we try to update anything, see if anything actually changed
    $spatialdata = SpatialData::get_record_data($record_uid,'record','single');
    if ($spatialdata->uid) { 
      // If no longer specified delete point
      if (empty($station_index) AND empty($northing) AND empty($easting) AND empty($elevation)) {
        $spatialdata->delete(true); 
        unset($spatialdata);
        $return = true; 
      }
      // If it's changed, update the point
      elseif ($station_index != $spatialdata->station_index OR $northing != $spatialdata->northing 
        OR $easting != $spatialdata->easting OR $elevation != $spatialdata->elevation) {
          $return = $spatialdata->update(array('station_index'=>$station_index,'northing'=>$northing,'easting'=>$easting,'elevation'=>$elevation));
      }
      else {
        $return = true;
      }
    }
    elseif ($station_index OR $northing OR $easting OR $elevation) { 
      $return = Spatialdata::Create(array('record'=>$record_uid,'station_index'=>$station_index,'northing'=>$northing,'easting'=>$easting,'elevation'=>$elevation,'type'=>'record'));
    }
    else {
      $return = true;
    }

    if (!$return) {
      Err::add('spatial_data','Error updating Spatial Data, please try again');
    }

		// Remove this object from the cache so the update shows properly
		$this->refresh(); 

    // Rebuild the ticket as values may have changed
    $ticket = new Content($record_uid,'ticket','record');
    Content::write($record_uid,'ticket',$ticket->filename);

    $site = $this->site->name; 
    $level = new Level($input['level']);

		$log_line = json_encode(array('Site'=>$site,'Name'=>$level->unit->name,'Level'=>$input['level'],'LSGUnit'=>$lsg_unit,
        'Station Index'=>$station_index,'XRFMatrix'=>$xrf_matrix_index,'Weight'=>$weight,'Height'=>$height,'Width'=>$width,
        'Thickness'=>$thickness,'Quanity'=>$quanity,'Material'=>$material,'Classification'=>$classification,'Feature'=>$input['feature'],
        'Notes'=>$notes,'User'=>\UI\sess::$user->username,'Update'=>date("r",$updated)));
		Event::record('record::update',$log_line); 
		return true; 

	} // update


  /**
   * validate
   * Take the input data and validate pass optional record_id
   */
	public static function validate($input,$record_id='') { 

    // Fill empty optional fields with null 
    $fields = array('xrf_matrix_index','xrf_artifact_index','weight','height','width','thickness','quanity');
    foreach ($fields as $key) { if (!isset($input[$key])) { $input[$key] = NULL; } }

    // If we were given the record for which these values are assoicated
    if ($record_id) { $record = new Record($record_id); }
		

    // Allow old stuff to remain behind
    if (empty($record_id)) {
      if (!Lsgunit::is_valid($input['lsg_unit'])) {
  			Err::add('lsg_unit','Invalid Lithostratigraphic Unit'); 
      }
    }
    else { 
      if (!Lsgunit::is_valid($input['lsg_unit']) AND $record->lsg_unit->name != $input['lsg_unit']) {
        Err::add('lsg_unit','Invalid Lithostratigraphic Unit');
      }
    }

    if (!isset($input['station_index'])) { $input['station_index'] = null; }

		// Station Index must be numeric
    if (!Field::validate('station_index',$input['station_index']) AND strlen($input['station_index'])) {
			Err::add('station_index','Station Index must be numeric'); 
    } 

    //FIXME: This should be standardize on the table name
    $input['rn'] = $input['station_index'];
    // Unique Spatial Record check
    if (!empty($input['rn'])) {
      if (!SpatialData::is_site_unique($input,$record_id)) {
        Err::add('RN','Duplicate RN or Northing/Easting/Elevation');
      }
    }

    // If they've set a RN then we need to make sure they didn't set northing,easting,elevation
    if (!empty($input['station_index'])) { 
    
      // If we are comparing it to an existing record
      if (isset($record->uid)) {   
		    if (strlen($input['northing']) AND $input['northing'] != $record->northing) {
          Err::add('northing','Northing can not be changed if the record has an RN'); 
        }
        if (strlen($input['easting']) AND $input['easting'] != $record->easting) { 
          Err::add('easting','Easting can not be changed if the record has an RN'); 
        }
        if (strlen($input['elevation']) AND $input['elevation'] != $record->elevation) { 
          Err::add('elevation','Elevation can not be changed if the record has an RN'); 
        }
      }
      else {
        if (strlen($input['northing'])) { 
          Err::add('northing','Northing can not be changed if the record has an RN'); 
        }
        if (strlen($input['easting'])) { 
          Err::add('easting','Easting can not be changed if the record has an RN'); 
        }
        if (strlen($input['elevation'])) { 
          Err::add('elevation','Elevation can not be changed if the record has an RN'); 
        }
      } // else no record_id
    } // if station_index
    // if no station index then just check format of northing/easting/elevation
    else { 
        if (intval($input['northing']) < 0 OR round($input['northing'],3) != $input['northing']) { 
          Err::add('northing','Northing must be numeric'); 
        }
        if (intval($input['easting']) < 0 OR round($input['easting'],3) != $input['easting']) { 
          Err::add('easting','Easting must be numeric'); 
        }
        if (intval($input['elevation']) < 0 OR round($input['elevation'],3) != $input['elevation']) { 
          Err::add('elevation','Elevation must be numeric'); 
        }
    }

		// XRF Matrix Index numeric
		if (!Field::validate('xrf_matrix_index',$input['xrf_matrix_index']) AND strlen($input['xrf_matrix_index'])) { 
			Err::add('xrf_matrix_index','XRF Matrix Index must be numeric'); 
		}

		// Weight, numeric floating point
		if (!Field::validate('weight',$input['weight'])) { 
			Err::add('weight','Weight must be numeric to a thousandth of a gram'); 
		} 

		// Height, numeric
		if (!Field::validate('height',$input['height'])) { 
			Err::add('height','Height must be numeric to a thousandth of a mm'); 
		} 

		// Width, numeric
		if (!Field::validate('width',$input['width'])) { 
			Err::add('width','Width must be numeric to a thousandth of a mm'); 
		} 

		// Thickness
		if (!Field::validate('thickness',$input['thickness'])) { 
			Err::add('thickness','Thickness must be numeric'); 
		} 
		
		// Quanity, numeric
		if (!Field::validate('quanity',$input['quanity']) AND strlen($input['quanity'])) { 
			Err::add('quanity','Quanity must be numeric'); 
		}
 
		// XRF Artifact Index, numeric
		if (!Field::validate('xrf_artifact_index',$input['xrf_artifact_index']) AND strlen($input['xrf_artifact_index'])) { 
			Err::add('xrf_artifact_index','XRF Artifact Index must be numeric'); 
		} 

		// Material, must be a valid UID
    if (empty($input['material'])) { 
      Err::add('material','Must specify material');
    }
		if (!empty($input['material'])) { 
			$material = new Material($input['material']); 
			if (!$material->name) { 
				Err::add('material','Invalid Material ID Specified, please refresh'); 
			} 

			// Classification must be in this material
			if (!$material->has_classification($input['classification'])) { 
				Err::add('classification','Invalid description for this material'); 
			} 


			$classification = new Classification($input['classification']); 

			if ($classification->name == "Other" AND !strlen($input['notes'])) { 
				Err::add('notes','Other description, but no notes specified'); 
			} 

		} // end if material 
		// Else we still need to check the classification, if its set
		elseif (strlen($input['classification'])) { 
			$classification = new Classification($input['classification']); 
			
			if ($classification->name == 'Other' AND !strlen($input['nodes'])) { 
				Err::add('notes','Other description, but no notes specified'); 
			} 
			if (!$classification->name) { 
				Err::add('classification','Invalid description');
			} 
		} // end if material 

    // Feature must exist first
    if (isset($input['feature'])) { 
      if ($input['feature']) {
        $feature_uid = Feature::get_uid_from_record($input['feature']); 
        if (!$feature_uid) {
    			Err::add('feature','Feature not found, please create feature record first'); 
    		} 
      }
    } // if feature specified

    // Krotovina must exist first
    if (isset($input['krotovina'])) {
      if ($input['krotovina']) {
        $krotovina_uid = Krotovina::get_uid_from_record($input['krotovina']);
        if (!$krotovina_uid) {
          Err::add('krotovina','Krotovina not found, please create Krotovina record first');
        }
      }
    }

    // The level must exist!
    if (strlen($input['level'])) {
      $level = new Level($input['level']);
      if (!$level->catalog_id) {
        Err::add('level','Level not found, please create level record first');
      }
    }

    // Make sure they entered only one of the three (krot/level/feature)
    $items = intval(!empty($input['krotovina'])) + intval(!empty($input['feature']));
    if ($items > 1) { 
      Err::add('association','Record must be associated with only either a feature or a krotovina');
    }

		// Notes... character limit
		if (strlen($input['notes']) > 500) { 
			Err::add('notes','Notes too long, this is not a novel'); 
		}

		// User
		$user = new User($input['user']); 
		if (!$user->username) { 
			Event::error('Record::Create',$input['user'] . ' passed, but does not match a known user'); 
			Err::add('general','User Unknown or disabled'); 
		}
		
		if (Err::occurred()) { return false; } 
		
    return true; 

	} // validate

	/** 
	 * last_created
	 * Return the last created record for the current site
	 */
	public static function last_created() { 

		$site = Dba::escape(\UI\sess::$user->site->uid); 
	
		$sql = "SELECT `uid` FROM `record` WHERE `site`='$site' ORDER BY `created` DESC LIMIT 1"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		$record = new Record($row['uid']); 

		return $record; 

	} // last_created

	/** 
	 * Delete
	 */
	public static function delete($uid) { 

		$record = new Record($uid); 

		// Unlink any media related to the record
		$images = $record->get_images(); 
		foreach ($images as $image) { 
			// Delete image and thumbnail if it exists
			$content = new Content($image['uid'],'image','record'); 
			if ($content->uid) { 
				$content->delete(); 
			}
		} 

    $media = $record->get_media(); 
    foreach ($media as $item) { 
      $content = new Content($item['uid'],'media','record'); 
      if ($content->uid) { 
        $content->delete(); 
      }
    } // end foreach media

		// If we've generated a ticket for this delete it
		$ticket = $record->get_ticket();
		if ($ticket->filename) { 
			$ticket->delete(); 
		} 

    // Remove spatial data assoicated with this record
    SpatialData::delete_by_record($uid,'record');

		$uid = Dba::escape($uid); 
		$sql = "DELETE FROM `record` WHERE `uid`='$uid' LIMIT 1"; 
		$db_results = Dba::write($sql); 

		return true; 
	} // delete 

  /**
    * get_custom_field
    * return the current value of the custom field
    */
  public function get_custom_field($name) { 

    if (isset($this->extra[$name])) {
      return $this->extra[$name];
    }

    return '';

  } // get_custom_field

	/**
	 * get_images
	 * FIXME: Do this using content?
	 * Gets a list of the images this record has
	 */
	public function get_images() { 

		$record_id = Dba::escape($this->uid); 
		$sql = "SELECT * FROM `image` WHERE `record`='$record_id'"; 
		$db_results = Dba::read($sql); 

		$results = array(); 

		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = $row; 
		} 

		return $results; 

	} // get_images

  /**
   * get_media
   * Return any misc media we have for this record
   */
  public function get_media() { 

    $record_id = Dba::escape($this->uid); 
    $sql = "SELECT * FROM `media` WHERE `record`='$record_id' AND `type`='media'";
    $db_results = Dba::read($sql); 

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row; 
    } 

    return $results; 

  } // get_media

	/**
	 * get_ticket
	 * returns the current ticket pdf
	 */
	public function get_ticket() { 

		$ticket = new Content($this->uid,'ticket','record'); 

		return $ticket; 

	} // get_ticket

  /**
   * get_user_last
   * Get the users last X records
   */
  public static function get_user_last($count,$uid='') { 

    if (!$uid) {
      $uid = \UI\sess::$user->uid;
    }

    $results = array();
    $uid = Dba::escape($uid);
    $count = abs(floor($count));

    $sql = "SELECT * FROM `record` WHERE `user`='$uid' AND `site`=? ORDER BY `created` DESC LIMIT $count";
    $db_results = Dba::read($sql,array(\UI\sess::$user->site->uid));

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid'];
      parent::add_to_cache('record',$row['uid'],$row);
    }

    return $results;

  } // get_user_last

} // end record class 
