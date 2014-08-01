<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
class Record extends database_object { 


  public $uid; // INTERNAL 
  public $site; // Site UID  
  public $catalog_id; // # of item unique to site
  public $inventory_id; // this is the built ID of the thingy from site + year + catalog id
  public $unit; 
  public $quad; 
  public $feature; 
  public $krotovina;
  public $level; 
  public $lsg_unit; 
  public $station_index; // LISTED AS RN in the interface
  public $xrf_matrix_index; 
  public $weight; 
  public $width; 
  public $height; 
  public $thickness; 
  public $quanity; 
  public $material; // FK
  public $classification; // FK
  public $xrf_artifact_index; 
  public $notes; 
  public $user_id; // The ID
  public $northing; // Northing from station info
  public $easting; // Easting from station info
  public $elevation; // Elevation from station info
	public $user; // The Object 
	public $created; 
	public $updated; 

	// Constructor
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 
		
		$row = $this->get_info($uid,'record'); 

		foreach ($row as $key=>$value) { $this->$key = $value; } 

		// Setup the Material and classification
		$this->material = new Material($this->material); 
		$this->classification = new Classification($this->classification); 
		$this->lsg_unit	= new lsgunit($this->lsg_unit); 
		$this->quad = new quad($this->quad); 
    $this->site = new site($this->site);
		$this->inventory_id = $this->site->name . '.' . date('Y',$this->created) . '-' . $this->catalog_id;
    $this->record = $this->site->name . '-' . $this->catalog_id;
		$this->user_id = $this->user; 
    $this->feature = new Feature($this->feature);
    $this->krotovina = new Krotovina($this->krotovina);
    $this->level = new Level($this->level);
		$this->user = new User($this->user); 

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

    while ($row = Dba::Fetch_assoc($db_results)) { 
      parent::add_to_cache('record',$row['uid'],$row); 
      $materials[$row['material']] = $row['material']; 
      $classifications[$row['classification']] = $row['classification']; 
      $users[$row['user']] = $row['user']; 
    } 
    
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

	// Create
	public static function create($input) { 

    // Clear any previous errors before we do the validatation
    Error::clear(); 

    // Set the site based on the session users current site
    $input['site'] = \UI\sess::$user->site->uid;

		// First verify the input to make sure
		// all of the fields are within acceptable tolerences 
		if (!Record::validate($input)) { 
			Error::add('general','Invalid Field Values - please check input'); 
			return false; 
		} 

		$db_results = false; 
		$times = 0; 
		$lock_sql = "LOCK TABLES `record` WRITE, `krotovina` READ, `feature` READ, `level` READ;"; 
		$unlock_sql = "UNLOCK TABLES"; 

		// Only wait 3 seconds for this, it shouldn't take that long
		while (!$db_results && $times < 3) { 

			// If we make it this far we're good to go, we need to figure out the next station ID
			$db_results = Dba::write($lock_sql); 
		
			if (!$db_results) { sleep(1); $times++; } 

		} 

		// If we never obtain the lock, then we can't go on
		if (!$db_results) { 
			Error::add('general','Database Read Failure, please resubmit'); 
			return false; 
		} 

		// Reset Row variable
		$row = array(); 

		// If no catalog ID is defined then we need to auto-inc it
		if (!$input['catalog_id']) { 
			$site = Dba::escape($input['site']); 
			$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`='$site' ORDER BY `catalog_id` DESC LIMIT 1"; 
			$db_results = Dba::read($catalog_sql); 
			$row = Dba::fetch_assoc($db_results); 	
			Dba::finish($db_results); 

			$catalog_id = $row['catalog_id']+1; 
		} 
		// Else we need to make sure it isn't a duplicate
		else { 
			$site = Dba::escape($input['site']); 
			$catalog_id = Dba::escape($input['catalog_id']); 
			$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`='$site' AND `catalog_id`='$catalog_id' LIMIT 1"; 
			$db_results = Dba::read($catalog_sql); 
			$row = Dba::fetch_assoc($db_results); 
			Dba::finish($db_results); 
			if ($row['catalog_id']) { 
				Error::add('general','Database Failure - Duplicate CatalogID - ' . $catalog_id); 
				$db_results = Dba::write($unlock_sql); 
				return false; 
			} 

		} 

    // We need the real UID of the following objects
    $level = new Level($input['level']);
    $feature_uid  = Feature::get_uid_from_record($input['feature']);
    $krotovina_uid = Krotovina::get_uid_from_record($input['krotovina']);

		// Insert the new record
    $site = Dba::escape(\UI\sess::$user->site->uid);
		$unit = Dba::escape($level->unit); 
		$level = Dba::escape($level->uid); 
		$lsg_unit = Dba::escape($input['lsg_unit']); 
		$xrf_matrix_index = Dba::escape($input['xrf_matrix_index']); 
		$weight = Dba::escape($input['weight']); 
		$height = Dba::escape($input['height']); 
		$width = Dba::Escape($input['width']); 
		$thickness = Dba::escape($input['thickness']); 
		$quanity = ($input['quanity'] == 0) ? '1' : Dba::escape($input['quanity']); // Default to Quanity 1 
		$material = Dba::escape($input['material']); 
		$classification = Dba::escape($input['classification']); 
		$notes = Dba::escape($input['notes']); 
		$xrf_artifact_index = Dba::escape($input['xrf_artifact_index']); 
		$quad = Dba::escape($level->quad->uid); 
		$feature = Dba::escape($feature_uid);  
    $krotovina = Dba::escape($krotovina_uid);
    $northing = Dba::escape($input['northing']); 
    $easting = Dba::escape($input['easting']); 
    $elevation = Dba::escape($input['elevation']); 
		$user = Dba::escape(\UI\sess::$user->uid); 
		$created = time(); 

		// This can be null needs to be handled slightly differently
		$station_index = $input['station_index'] ? "'" . Dba::escape($input['station_index']) . "'" : "NULL"; 
		
		$sql = "INSERT INTO `record` (`site`,`catalog_id`,`unit`,`level`,`lsg_unit`,`station_index`,`xrf_matrix_index`,`weight`,`height`,`width`,`thickness`,`quanity`,`material`,`classification`,`notes`,`xrf_artifact_index`,`quad`,`feature`,`krotovina`,`user`,`created`,`northing`,`easting`,`elevation`) " . 
			"VALUES ('$site','$catalog_id','$unit',$level,'$lsg_unit',$station_index,'$xrf_matrix_index','$weight','$height','$width','$thickness','$quanity','$material','$classification','$notes','$xrf_artifact_index','$quad','$feature','$krotovina','$user','$created','$northing','$easting','$elevation')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Error::add('general','Unknown Error inserting record into database'); 
			$db_results = Dba::write($unlock_sql); 
			return false; 
		} 
		$insert_id = Dba::insert_id(); 

		$db_results = Dba::write($unlock_sql); 

    $legend_line = "Site,Catalog ID,Unit,Level,LSG Unit,RN,XRF Matrix Index, Weight (grams),Height(mm),Width(mm),Thickness(mm),Quanity,Material,Classification,Quad,Feature ID,Krotovina ID,User,Date";
    Event::record('ADD-LEGEND',$legend_line);
		$log_line = "$site,$catalog_id,$unit," . $input['level'] . ",$lsg_unit,$station_index,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$quad," . $input['feature'] ."," . $input['krotovina'] . ",\"" . addslashes($notes) . "\"," . \UI\sess::$user->username . ",\"" . date("r",$created) . "\"";
		Event::record('ADD',$log_line); 

    // Clear any cache that might have gotten created

		// We're sure we've got a record so lets generate our QR code. 
		Content::write($insert_id,'qrcode'); 

		return $insert_id; 

	} // create	

	// Update
	public function update($input) { 

    // Clear any previous errors before we do the validatation
    Error::clear(); 

    // Set the site, they can't change this
    $input['site'] = $this->site->uid;
  
    // First verify the input to make sure
    // all of the fields are within acceptable tolerences 
    if (!Record::validate($input,$this->uid)) {
      Error::add('general','Invalid Field Values - Please check your input again');
      return false;
    }

    // We need the real UID of the following objects
    $level = new Level($input['level']);
    $level_uid = $level->uid;
    $feature_uid  = Feature::get_uid_from_record($input['feature']);
    $krotovina_uid = Krotovina::get_uid_from_record($input['krotovina']);

		$lsg_unit = Dba::escape($input['lsg_unit']); 
		$xrf_matrix_index = Dba::escape($input['xrf_matrix_index']); 
		$weight = Dba::escape($input['weight']); 
		$height = Dba::escape($input['height']); 
		$width = Dba::Escape($input['width']); 
		$thickness = Dba::escape($input['thickness']); 
		$quanity = Dba::escape($input['quanity']); 
		$material = Dba::escape($input['material']); 
		$classification = Dba::escape($input['classification']); 
		$notes = Dba::escape($input['notes']); 
		$xrf_artifact_index = Dba::escape($input['xrf_artifact_index']); 
		$feature = Dba::escape($feature_uid); 
    $krotovina = Dba::escape($krotovina_uid);
    $northing = isset($input['northing']) ? Dba::escape($input['northing']) : Dba::escape($this->northing); 
    $easting = isset($input['easting']) ? Dba::escape($input['easting']) : Dba::escape($this->easting); 
    $elevation = isset($input['elevation']) ? Dba::escape($input['elevation']) : Dba::escape($this->elevation); 
		$user = Dba::escape(\UI\sess::$user->uid); 
		$updated = time(); 
		$record_uid = Dba::escape($this->uid); 

		// Allow this to be null
		$station_index = $input['station_index'] ? "'" . Dba::escape($input['station_index']) . "'" : "NULL"; 
		$level = $input['level'] ? "'" . Dba::escape($level_uid) . "'" : "NULL"; 

		$sql = "UPDATE `record` SET `level`=$level, `lsg_unit`='$lsg_unit', `station_index`=$station_index, " . 
			"`xrf_matrix_index`='$xrf_matrix_index', `weight`='$weight', `height`='$height', `width`='$width', `thickness`='$thickness', " . 
			"`quanity`='$quanity', `material`='$material', `classification`='$classification', `notes`='$notes', `xrf_artifact_index`='$xrf_artifact_index', " . 
			"`user`='$user', `updated`='$updated', `feature`='$feature', `northing`='$northing', `easting`='$easting', `elevation`='$elevation', " . 
      "`krotovina`='$krotovina' " .
			"WHERE `uid`='$record_uid'"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Error::add('general','Database Error, please try again'); 
			return false; 
		} 

		// Remove this object from the cache so the update shows properly
		$this->refresh(); 

    // Rebuild the ticket as values may have changed
    $ticket = new Content($record_uid,'ticket');
    Content::write($record_uid,'ticket',$ticket->filename);

    $site = $this->site->name; 

		$log_line = "$site,$catalog_id,$unit," . $input['level']. ",$lsg_unit,$station_index,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$quad," . $input['feature'] . ",\"" . addslashes($notes) . "\"," . \UI\sess::$user->username . ",\"" . date("r",$created) . "\"";
		Event::record('UPDATE',$log_line); 

		return true; 

	} // update


  /**
   * validate
   * Take the input data and validate pass optional record_id
   */
	public static function validate($input,$record_id='') { 

    // If we were given the record for which these values are assoicated
    if ($record_id) { $record = new Record($record_id); }
		
    // Make sure this user is allowed to create records in this site (write)
    if (!Access::has('site','write',$input['site'])) { 
      Error::add('site','Insufficient site permission, unable to create record');
    }

		// lsg_unit, numeric less then 50
		if ((!in_array($input['lsg_unit'],array_keys(lsgunit::$values)) OR $input['lsg_unit'] > 50) AND strlen($input['lsg_unit'])) { 
			Error::add('lsg_unit','Invalid Lithostratigraphic Unit'); 
		}

		// Station Index must be numeric
		if ((!is_numeric($input['station_index']) OR $input['station_index'] <= 0) AND strlen($input['station_index'])) { 
			Error::add('station_index','Station Index must be numeric'); 
		} 

		// Make sure the station index is unique within this site, but only if specified
		if (strlen($input['station_index'])) { 
			$sql = "SELECT * FROM `record` WHERE `station_index`='" . Dba::escape($input['station_index']) . "' AND `site`='" . Dba::escape($input['site']) . "'"; 
			$db_results = Dba::read($sql); 
			$row = Dba::fetch_assoc($db_results); 

			if ($row['uid'] AND $row['uid'] != $input['record_id']) { 
				Error::add('station_index','Duplicate - Station Index must be unique'); 
			} 
		} // end if station index is specified

    // If they've set a RN then we need to make sure they didn't set northing,easting,elevation
    if (strlen($input['station_index'])) { 
    
      // If we are comparing it to an existing record
      if ($record->uid) {   
		    if (isset($input['northing']) AND $input['northing'] != $record->northing) {
          Error::add('northing','Northing can not be changed if the record has an RN'); 
        }
        if (isset($input['easting']) AND $input['easting'] != $record->easting) { 
          Error::add('easting','Easting can not be changed if the record has an RN'); 
        }
        if (isset($input['elevation']) AND $input['elevation'] != $record->elevation) { 
          Error::add('elevation','Elevation can not be changed if the record has an RN'); 
        }
      }
      else {
        if (strlen($input['northing'])) { 
          Error::add('northing','Northing can not be changed if the record has an RN'); 
        }
        if (strlen($input['easting'])) { 
          Error::add('easting','Easting can not be changed if the record has an RN'); 
        }
        if (strlen($input['elevation'])) { 
          Error::add('elevation','Elevation can not be changed if the record has an RN'); 
        }
      } // else no record_id
    } // if station_index
    // if no station index then just check format of northing/easting/elevation
    else { 
        if (intval($input['northing']) < 0 OR round($input['northing'],3) != $input['northing']) { 
          Error::add('northing','Northing must be numeric'); 
        }
        if (intval($input['easting']) < 0 OR round($input['easting'],3) != $input['easting']) { 
          Error::add('easting','Easting must be numeric'); 
        }
        if (intval($input['elevation']) < 0 OR round($input['elevation'],3) != $input['elevation']) { 
          Error::add('elevation','Elevation must be numeric'); 
        }
    }
		// XRF Matrix Index numeric
		if (!is_numeric($input['xrf_matrix_index']) AND strlen($input['xrf_matrix_index'])) { 
			Error::add('xrf_matrix_index','XRF Matrix Index must be numeric'); 
		}

		// Weight, numeric floating point
		if (round($input['weight'],3) != $input['weight'] AND strlen($input['weight'])) { 
			Error::add('weight','Weight must be numeric to a thousandth of a gram'); 
		} 

		// Height, numeric
		if (round($input['height'],3) != $input['height'] AND strlen($input['height'])) { 
			Error::add('height','Height must be numeric to a thousandth of an mm'); 
		} 

		// Width, numeric
		if (round($input['width'],3) != $input['width'] AND strlen($input['width'])) { 
			Error::add('width','Length must be numeric to a thousandth of an mm'); 
		} 

		// Thickness
		if (round($input['thickness'],3) != $input['thickness'] AND strlen($input['thickness'])) { 
			Error::add('thickness','Thickness must be numeric'); 
		} 
		
		// Quanity, numeric
		if (!is_numeric($input['quanity']) AND strlen($input['quanity'])) { 
			Error::add('quanity','Quanity must be numeric'); 
		}
 
		// XRF Artifact Index, numeric
		if (!is_numeric($input['xrf_artifact_index']) AND strlen($input['xrf_artifact_index'])) { 
			Error::add('xrf_artifact_index','XRF Artifact Index must be numeric'); 
		} 

		// Material, must be a valid UID
		if (strlen($input['material'])) { 
			$material = new Material($input['material']); 
			if (!$material->name) { 
				Error::add('material','Invalid Material ID Specified, please refresh'); 
			} 

			// Classification must be in this material
			if (!$material->has_classification($input['classification'])) { 
				Error::add('classification','Invalid description for this material'); 
			} 


			$classification = new Classification($input['classification']); 

			if ($classification->name == "Other" AND !strlen($input['notes'])) { 
				Error::add('notes','Other description, but no notes specified'); 
			} 

		} // end if material 
		// Else we still need to check the classification, if its set
		elseif (strlen($input['classification'])) { 
			$classification = new Classification($input['classification']); 
			
			if ($classification->name == 'Other' AND !strlen($input['nodes'])) { 
				Error::add('notes','Other description, but no notes specified'); 
			} 
			if (!$classification->name) { 
				Error::add('classification','Invalid description');
			} 
		} // end if material 

    // Feature must exist first
    if (strlen($input['feature'])) { 
      $feature_uid = Feature::get_uid_from_record($input['feature']); 
      if (!$feature_uid) {
  			Error::add('Feature','Feature not found, please create feature record first'); 
  		} 
    } // if feature specified

    // Krotovina must exist first
    if (strlen($input['krotovina'])) {
      $krotovina_uid = Krotovina::get_uid_from_record($input['krotovina']);
      if (!$krotovina_uid) {
        Error::add('Krotovina','Krotovina not found, please create Krotovina record first');
      }
    }

    // The level must exist!
    if (strlen($input['level'])) {
      $level = new Level($input['level']);
      if (!$level->catalog_id) {
        Error::add('Level','Level not found, please create level record first');
      }
    }
    else { 
      Error::add('Level','Level must be specified for all records');
    }

    // Make sure they entered only one of the three (krot/level/feature)
    $items = intval(!empty($input['krotovina'])) + intval(!empty($input['feature']));
    if ($items > 1) { 
      Error::add('Association','Record must be associated with only either a feature or a krotovina');
    }

		// Notes... character limit
		if (strlen($input['notes']) > 500) { 
			Error::add('notes','Notes too long, this is not a novel'); 
		}

		// User
		$user = new User($input['user']); 
		if (!$user->username) { 
			Event::error('Record::Create',$input['user'] . ' passed, but does not match a known user'); 
			Error::add('general','User Unknown or disabled'); 
		}
		
		if (Error::occurred()) { return false; } 

		return true; 

	} // validate

	/** 
	 * Export
	 * This exports all of the records
	 */
	public static function export($type,$site) { 

		$site = Dba::escape($site); 
		$sql = "SELECT * FROM `record` WHERE `site`='$site'"; 
		$db_results = Dba::read($sql); 	

		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = new Record($row['uid']); 
		} 

		switch ($type) { 
			default: 
			case 'csv': 
				echo "site,catalog id,unit,level,litho unit,station index,xrf matrix index,weight,height,width,thickness,quantity,material,classification,quad,feature,notes,created\n"; 
				foreach ($results as $record) { 
					$site = $record->site->name; 
					$record->notes = str_replace(array("\r\n", "\n", "\r"),' ',$record->notes); 
					echo "$site," . $record->catalog_id . "," . $record->unit . "," . $record->level->record . "," . lsgunit::$values[$record->lsg_unit] . "," . 
						$record->station_index . "," . $record->xrf_matrix_index . "," . $record->weight . "," . $record->height . "," . 
						$record->width . "," . $record->thickness . "," . $record->quanity . "," . $record->material->name . "," . 
						trim($record->classification->name) . "," . quad::$values[$record->quad] . "," . $record->feature->record . ",\"" . 
						addslashes($record->notes) . "\"," . date("m-d-Y h:i:s",$record->created) . "\n"; 
				} // end foreach 

			break; 
		} 

	} // export

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
			$content = new Content($image['uid'],'image'); 
			if ($content->uid) { 
				$content->delete(); 
			}
		} 

    $media = $record->get_media(); 
    foreach ($media as $item) { 
      $content = new Content($item['uid'],'media'); 
      if ($content->uid) { 
        $content->delete(); 
      }
    } // end foreach media

		// If we've generated a ticket for this delete it
		$ticket = $record->get_ticket();
		if ($ticket->filename) { 
			$ticket->delete(); 
		} 

		$uid = Dba::escape($uid); 
		$sql = "DELETE FROM `record` WHERE `uid`='$uid' LIMIT 1"; 
		$db_results = Dba::write($sql); 

		return true; 
	} // delete 

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

		$ticket = new Content($this->uid,'ticket'); 

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

    $sql = "SELECT * FROM `record` WHERE `user`='$uid' ORDER BY `created` DESC LIMIT $count";
    $db_results = Dba::read($sql);

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid'];
      parent::add_to_cache('record',$row['uid'],$row);
    }

    return $results;

  } // get_user_last

} // end record class 
