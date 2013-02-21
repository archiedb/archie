<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
class Record extends database_object { 


	public $uid; // INTERNAL 
	public $site; // 10IH70.2011 
	public $catalog_id; // # of item unique to site
	public $inventory_id; // this is the built ID of the thingy from site + year + catalog id
	public $unit; 
	public $quad; 
	public $feature; 
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
	public $user; // The Object 
	public $created; 
	public $updated; 

	// Constructor
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 
		
		$row = $this->get_info($uid); 
	
		foreach ($row as $key=>$value) { $this->$key = $value; } 

		// Setup the Material and classification
		$this->material = new Material($this->material); 
		$this->classification = new Classification($this->classification); 
		$this->inventory_id = $this->site . '.' . date('Y',$this->created) . '-' . $this->catalog_id;
		$this->user_id = $this->user; 
		$this->user = new User($this->user); 

		return true; 

	} // constructor

	// Create
	public static function create($input) { 

		// First verify the input to make sure
		// all of the fields are within acceptable tolerences 
		if (!Record::validate($input)) { 
			Error::add('general','Invalid Field Values - please check input'); 
			return false; 
		} 

		$db_results = false; 
		$times = 0; 
		$lock_sql = "LOCK TABLES `record` WRITE;"; 
		$unlock_sql = "UNLOCK TABLES"; 

		// Only wait 5 seconds for this, it shouldn't take that long
		while (!$db_results && $times < 5) { 

			// If we make it this far we're good to go, we need to figure out the next station ID
			$db_results = Dba::query($lock_sql); 
		
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
			$site = Dba::escape(Config::get('site')); 
			$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`='$site' ORDER BY `catalog_id` DESC LIMIT 1"; 
			$db_results = Dba::query($catalog_sql); 
			$row = Dba::fetch_assoc($db_results); 	
			Dba::finish($db_results); 

			$catalog_id = $row['catalog_id']+1; 
		} 
		// Else we need to make sure it isn't a duplicate
		else { 
			$site = Dba::escape($input['site']); 
			$catalog_id = Dba::escape($input['catalog_id']); 
			$catalog_sql = "SELECT `catalog_id` FROM `record` WHERE `site`='$site' AND `catalog_id`='$catalog_id' LIMIT 1"; 
			$db_results = Dba::query($catalog_sql); 
			$row = Dba::fetch_assoc($db_results); 
			Dba::finish($db_results); 
			if ($row['catalog_id']) { 
				Error::add('general','Database Failure - Duplicate CatalogID - ' . $catalog_id); 
				$db_results = Dba::query($unlock_sql); 
				return false; 
			} 

		} 

		// Insert the new record
		$unit = Dba::escape($input['unit']); 
		$level = Dba::escape($input['level']); 
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
		$quad = Dba::escape($input['quad']); 
		$feature = Dba::escape($input['feature']);  
		$user = Dba::escape($GLOBALS['user']->uid); 
		$created = time(); 

		// This can be null needs to be handled slightly differently
		$station_index = $input['station_index'] ? "'" . Dba::escape($input['station_index']) . "'" : "NULL"; 
		$level = $level ? "'" . Dba::escape($input['level']) . "'" : "NULL"; 
		
		$sql = "INSERT INTO `record` (`site`,`catalog_id`,`unit`,`level`,`lsg_unit`,`station_index`,`xrf_matrix_index`,`weight`,`height`,`width`,`thickness`,`quanity`,`material`,`classification`,`notes`,`xrf_artifact_index`,`quad`,`feature`,`user`,`created`) " . 
			"VALUES ('$site','$catalog_id','$unit',$level,'$lsg_unit',$station_index,'$xrf_matrix_index','$weight','$height','$width','$thickness','$quanity','$material','$classification','$notes','$xrf_artifact_index','$quad','$feature','$user','$created')"; 
		$db_results = Dba::query($sql); 

		if (!$db_results) { 
			Error::add('general','Unknown Error inserting record into database'); 
			$db_results = Dba::query($unlock_sql); 
			return false; 
		} 
		$insert_id = Dba::insert_id(); 

		$db_results = Dba::query($unlock_sql); 

		$log_line = "$site,$catalog_id,$unit,$level,$lsg_unit,$station_index,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$quad,$feature\"" . addslashes($notes) . "\"," . $GLOBALS['user']->username . ",\"" . date("r",$created) . "\"";
		Event::record('ADD',$log_line); 

		// We're sure we've got a record so lets generate our QR code. 
		Content::write($insert_id,'qrcode'); 

		return $insert_id; 

	} // create	

	// Update
	public function update($input) { 
                // First verify the input to make sure
                // all of the fields are within acceptable tolerences 
                if (!Record::validate($input)) {
                        Error::add('general','Invalid Field Values - Please check your input again');
                        return false;
                }

		$unit = Dba::escape($input['unit']); 
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
		$quad = Dba::escape($input['quad']); 
		$feature = Dba::escape($input['feature']); 
		$user = Dba::escape($GLOBALS['user']->uid); 
		$updated = time(); 
		$record_uid = Dba::escape($this->uid); 

		// Allow this to be null
		$station_index = $input['station_index'] ? "'" . Dba::escape($input['station_index']) . "'" : "NULL"; 
		$level = $input['level'] ? "'" . Dba::escape($input['level']) . "'" : "NULL"; 

		$sql = "UPDATE `record` SET `unit`='$unit', `level`=$level, `lsg_unit`='$lsg_unit', `station_index`=$station_index, " . 
			"`xrf_matrix_index`='$xrf_matrix_index', `weight`='$weight', `height`='$height', `width`='$width', `thickness`='$thickness', " . 
			"`quanity`='$quanity', `material`='$material', `classification`='$classification', `notes`='$notes', `xrf_artifact_index`='$xrf_artifact_index', " . 
			"`user`='$user', `updated`='$updated', `quad`='$quad', `feature`='$feature' " . 
			"WHERE `uid`='$record_uid'"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Error::add('general','Database Error, please try again'); 
			return false; 
		} 

		$log_line = "$site,$catalog_id,$unit,$level,$lsg_unit,$station_index,$xrf_matrix_index,$weight,$height,$width,$thickness,$quanity,$material,$classification,$quad,$feature\"" . addslashes($notes) . "\"," . $GLOBALS['user']->username . ",\"" . date("r",$created) . "\"";
		Event::record('UPDATE',$log_line); 

		return true; 

	} // update


	// validate
	// Validate the input for the record and make sure ints are ints
	public static function validate($input) { 
		
		// Unit A-Z
		if (preg_match("/[^A-Za-z]/",$input['unit'])) { 
			Error::add('unit','UNIT must be A-Z'); 
		} 

		// Level numeric, most likely less then 50
		if ((!is_numeric($input['level']) OR $input['level'] > 50) AND strlen($input['level'])) { 
			Error::add('level','Level must be numeric and less than 50'); 
		} 

		// lsg_unit, numeric less then 50
		if ((!in_array($input['lsg_unit'],array_keys(lsgunit::$values)) OR $input['lsg_unit'] > 50) AND strlen($input['lsg_unit'])) { 
			Error::add('lsg_unit','Invalid Lithostratigraphic Unit'); 
		}

		// Station Index must be numeric
		if (!is_numeric($input['station_index']) AND strlen($input['station_index'])) { 
			Error::add('station_index','Station Index must be numeric'); 
		} 

		// Make sure the station index is unique within this site, but only if specified
		if (strlen($input['station_index'])) { 
			$sql = "SELECT * FROM `record` WHERE `station_index`='" . Dba::escape($input['station_index']) . "' AND `site`='" . Dba::escape(Config::get('site')) . "'"; 
			$db_results = Dba::read($sql); 
			$row = Dba::fetch_assoc($db_results); 

			if ($row['uid'] AND $row['uid'] != $input['record_id']) { 
				Error::add('station_index','Duplicate - Station Index must be unique'); 
			} 
		} // end if station index is specified
		
		// XRF Matrix Index numeric
		if (!is_numeric($input['xrf_matrix_index']) AND strlen($input['xrf_matrix_index'])) { 
			Error::add('xrf_matrix_index','XRF Matrix Index must be numeric'); 
		} 

		// Weight, numeric floating point
		if (!is_numeric($input['weight']) AND strlen($input['weight'])) { 
			Error::add('weight','Weight must be numeric and in grams'); 
		} 

		// Height, numeric
		if (!is_numeric($input['height']) AND strlen($input['height'])) { 
			Error::add('height','Height must be numeric'); 
		} 

		// Width, numeric
		if (!is_numeric($input['width']) AND strlen($input['width'])) { 
			Error::add('width','Length must be numeric'); 
		} 

		// Thickness
		if (!is_numeric($input['thickness']) AND strlen($input['thickness'])) { 
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

		// The quad has to exist
		if (!in_array($input['quad'],array_keys(quad::$values)) AND strlen($input['quad'])) { 
			Error::add('Quad','Invalid Quad selected'); 
		} 

		if (!preg_match("/[A-Za-z0-9]/",$input['feature']) AND strlen($input['feature'])) { 
			Error::add('Feature','Feature must be numeric'); 
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
	public static function export($type) { 

		$site = Dba::escape(Config::get('site')); 
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
					$site = Config::get('site'); 
					$record->notes = str_replace(array("\r\n", "\n", "\r"),' ',$record->notes); 
					echo "$site," . $record->catalog_id . "," . $record->unit . "," . $record->level . "," . lsgunit::$values[$record->lsg_unit] . "," . 
						$record->station_index . "," . $record->xrf_matrix_index . "," . $record->weight . "," . $record->height . "," . 
						$record->width . "," . $record->thickness . "," . $record->quanity . "," . $record->material->name . "," . 
						trim($record->classification->name) . "," . quad::$values[$record->quad] . "," . $record->feature . ",\"" . 
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

		$site = Dba::escape(Config::get('site')); 
	
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
			$content = new Content($image['uid'],'record'); 
			if ($content->uid) { 
				$content->delete(); 
			}
		} 

		$uid = Dba::escape($uid); 
		$sql = "DELETE FROM `record` WHERE `uid`='$uid' LIMIT 1"; 
		$db_results = Dba::write($sql); 

		return true; 
	} // delete 

	/**
	 * get_images
	 * FIXME: Do this using content!
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

} // end record class 
