<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 


/**
 * Content 
 * This class deals with "media" assoicated with a record
 * it does both the write and read
 */
class content extends database_object {

  public $uid; // UID of object
  public $filename; // "UID" for this class, the real filename
  public $extension; // filename extension
  public $thumbnail; 
  public $mime; // Mime type of this image
  public $parentuid; // Parent UID this is assoicated with
  public $notes; 
  public $user; 
  public $type; // Type of object, most likely an image for now
  public $source; // Raw data of the object
  private $valid_types = array('image','qrcode','ticket','media','3dmodel'); 

  public function __construct($uid='',$type) {

    if (!in_array($type,$this->valid_types)) {
      Event::error('general','Invalid Content Type Specified');
      return false;
    }

    $this->uid = intval($uid);
    $this->type = $type;
    $this->{"load_".$type."_data"}($uid);

  } // construct

  /**
   * build_cache
   * Take a type an array of objects and cache em
   */
  public static function build_cache($objects,$type) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')'; 

    if ($idlist == '()') { return false; }

    switch ($type) { 
      case 'media':
      case '3dmodel':
        $table_name = 'media';
      break;
      case 'image':
        $table_name = 'image';
      break;
    }

    $sql = 'SELECT * FROM `' . $table_name . '` WHERE `uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache($table_name,$row['uid'],$row); 
    }

    return true; 

  } // build_cache

  /**
   * refresh
   */
  public function refresh() { 

    parent::remove_from_cache($this->type,$this->uid); 
    $this->__construct($this->uid,$this->type); 

  } // refresh

	/**
	 * load_image_data
	 * This loads a record image from its UID
	 */
	private function load_image_data($uid) {

    $retval = true; 

    // Use cache if it exists
    if (parent::is_cached('image',$uid)) { 
      $row = parent::get_from_cache('image',$uid);
    }
    else {
      $uid = Dba::escape($uid);
      $sql = "SELECT * FROM `image` WHERE `uid`='$uid'";
      $db_results = Dba::read($sql);
      $row = Dba::fetch_assoc($db_results);
      if (!count($row)) { $retval = false; }
      parent::add_to_cache('image',$uid,$row); 
    }

    $info = pathinfo($row['data']); 

    $this->extension  = $info['extension']; 
    $this->filename   = Config::get('data_root') . '/' . $row['data'];
    $this->thumbnail  = Config::get('data_root') . '/' . $row['data'] . '.thumb';
    $this->mime       = $row['mime'];
    $this->parentuid  = $row['record'];
    $this->notes      = $row['notes']; 
    $this->user       = $row['user']; 
    $this->record_type = $row['type'];

		return $retval; 

	} // load_image_data

	/** 
	 * load_qrcode_data
	 * This loads the qrcode image from the record info
	 * UID is the record uid
	 */
	private function load_qrcode_data($uid) { 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `media` WHERE `record`='$uid' AND `type`='qrcode'";
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		// We didn't find anything :(
		if (!isset($row['uid'])) { return false; }

		$this->filename = Config::get('data_root') . '/' . $row['filename'];
		$this->uid	= $row['uid'];
		$this->parentuid = $row['record']; 
		$this->mime	= 'image/png'; 
		
		return true; 

	} // load_qrcode_data

	/**
	 * load_ticket_data
	 * This loads the ticket pdf info from media 
	 */
	private function load_ticket_data($uid) { 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `media` WHERE `record`='$uid' AND `type`='ticket'";
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

    if (!isset($row['uid'])) { return false; }

		$this->filename = Config::get('data_root') . '/' . $row['filename']; 
		$this->uid	= $row['uid']; 
		$this->parentuid = $row['record']; 
		$this->mime	= 'application/pdf'; 

		return true;  

	} // load_ticket_data

  /**
   * load_media_data
   * Placeholder
   */
  private function load_media_data($uid) { 

    // Check and see if this is cached
    if (parent::is_cached('media',$uid)) { 
      $row = parent::get_from_cache('media',$uid); 
    } 
    else {
      $uid = Dba::escape($uid); 
      $sql = "SELECT * FROM `media` WHERE `uid`='$uid' AND `type`='media'"; 
      $db_results = Dba::read($sql); 
      $row = Dba::fetch_assoc($db_results); 
      parent::add_to_cache('media',$uid,$row); 
    }

    if (!isset($row['uid'])) { return false; }

    $this->filename = Config::get('data_root') . '/' . $row['filename']; 
    $this->uid = $row['uid']; 
    $this->parentuid = $row['record']; 
    $this->user = $row['user']; 
    $this->notes = $row['notes']; 

    // We need the extension
    $info = pathinfo($row['filename']); 

    $this->thumbnail = $info['dirname'] . '/' . $info['filename'] . '.png'; 
    $this->extension = $info['extension']; 
    $this->mime = Content::get_mime($info['extension']); //FIXME: I should have something for this?  

  } // load_media_data

  /**
   * load_3dmodel_data
   * This is kind of a catch all class for general non-image files
   */
  private function load_3dmodel_data($uid) { 

    // Check and see if this is cached
    if (parent::is_cached('media',$uid)) { 
      $row = parent::get_from_cache('media',$uid); 
    } 
    else {
      $uid = Dba::escape($uid); 
      $sql = "SELECT * FROM `media` WHERE `uid`='$uid' AND `type`='3dmodel'"; 
      $db_results = Dba::read($sql); 
      $row = Dba::fetch_assoc($db_results); 
      parent::add_to_cache('media',$uid,$row); 
    }

    if (!isset($row['uid'])) { return false; }

    $this->filename = Config::get('data_root') . '/' . $row['filename']; 
    $this->uid = $row['uid']; 
    $this->parentuid = $row['record']; 
    $this->user = $row['user']; 
    $this->notes = $row['notes']; 

    // We need the extension
    $info = pathinfo($row['filename']); 

    $this->thumbnail = Config::get('data_root') . '/' . $info['dirname'] . '/' . $info['filename'] . '.png'; 
    $this->extension = $info['extension']; 
    $this->mime = Content::get_mime($info['extension']); //FIXME: I should have something for this?  

  } // load_3dmodel_data

	// Reads in and returns the source of this file
	public function source() { 

		$data = file_get_contents($this->filename); 
		return $data; 	

	} // source

  /**
   * thumbnail
   * return the thumbnail for this content (like source)
   */ 
  public function thumbnail() {

    if (file_exists($this->thumbnail)) { 
      $data = file_get_contents($this->thumbnail); 
    }
    else {
      // Some things have a default!
      if ($this->type == '3dmodel') { 
        $data = file_get_contents(Config::get('prefix') .'/images/3dmodel.png');
      }
      else { 
        $data = '';
      }
    }

    return $data; 

  } // thumbnail

	// This writes out the specified data 
	public static function write($uid,$type,$data='',$mime_type='',$options='',$record_type='') { 
		
    switch ($record_type) { 
      case 'level':
        $record = new Level($uid);
				$extension = self::get_extension($mime_type); 
        $filename = self::generate_filename($record->site . '-level-' . $record->record,$extension);
      break;
      case 'record':
      default:
    		$record = new Record($uid); 
				$extension = self::get_extension($mime_type); 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
      break;
    }

		// First we need to generate a filename /SITE/YYYY/RECORD-MMDDHHMMSS
		switch ($type) { 
			case 'qrcode':
				// If data is passed, use that as filename
				$filename = strlen($data) ? $data : self::generate_filename($record->site . '-qrcode-' . $record->catalog_id,'png'); 
				$results = self::write_qrcode($uid,$filename,$data); 
			break; 
			case 'ticket': 
				// If data is passed, use that as filename
				$filename = strlen($data) ? $data : self::generate_filename($record->site . '-ticket-' . $record->catalog_id,'pdf');
				$results = self::write_ticket($record,$filename,$data); 
			break; 
      case '3dmodel':
        $extension = $mime_type;
        $filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
        $results = self::write_3dmodel($uid,$data,$filename,$options); 
      break;
      case 'media': 
        $extension = $mime_type; 
        $filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
        $results = self::write_media($uid,$data,$filename,$options); 
      break; 
			case 'image': 
				$results = self::write_image($uid,$data,$filename,$mime_type,$options,$record_type); 
      break;
		} 

		return $results; 

	} // write

	/**
	 * write a record image
	 */
	private static function write_image($uid,$data,$filename,$mime_type,$notes,$type) {

		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 
		$results = fwrite($handle,$data); 

    fclose($handle); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
		$uid = Dba::escape($uid); 
    $type = Dba::escape($type);
		$mime_type = Dba::escape($mime_type); 
    $notes = Dba::escape($notes); 
    $user = Dba::escape(\UI\sess::$user->uid); 
		$sql = "INSERT INTO `image` (`data`,`record`,`mime`,`user`,`notes`,`type`) VALUES ('$filename','$uid','$mime_type','$user','$notes','$type')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Event:error('general','Error inserting record of Image into Database'); 
			return false; 
		} 

    $image_uid = Dba::insert_id(); 
    Content::regenerate_thumb($image_uid); 

		return $db_results; 

	} // write_image

	/**
	 * write_qrcode
	 * Generate and save a qrcode
	 */
	private static function write_qrcode($uid,$filename,$update_record) { 

		$qrcode_data = Config::get('web_path') . '/records/view/' . $uid;
		QRcode::png($qrcode_data,$filename,'H','2',2); 
		
		if (!is_file($filename)) { 
			Event::error('QRCode','Error unable to generate qrcode [' . $filename . ' - ' . $qrcode_data .'] ');
			return false; 
		} 

		// Insert a record of this into the media table (why do we have an images table??!@)
		$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
		$uid = Dba::escape($uid); 
		$type = 'qrcode';

		if (!$update_record) {
			$sql = "INSERT INTO `media` (`filename`,`type`,`record`) VALUES ('$filename','$type','$uid')"; 
			$db_results = Dba::write($sql); 

			if (!$db_results) { 
				Event::error('Database','Unknown Database Error inserting QRCode'); 
				return false; 
			} 
		} // new record

		return true;  
	
	} // write_qrcode

	/** 
	 * write_ticket
	 * This genreates a 3.5" x 1" pdf with QRcode
	 * plus whatever else we can fit
	 */
	private static function write_ticket(&$record,$filename,$update_record) {

		$pdf = new FPDF();
		$pdf->AddPage('L',array('88.9','25.4'));

		// We need the QRcode filename here
		$qrcode = new Content($record->uid,'qrcode'); 
		$pdf->Image($qrcode->filename,'0','0','25.4','25.4'); 
		$pdf->SetFont('Courier','B'); 
		$pdf->SetFontSize('8'); 
		$pdf->Text('25','4','SITE:' . $record->site);
		$pdf->Text('52','4','UNIT:' . $record->unit); 
		$pdf->Text('25','8','LVL:' . $record->level);
		$pdf->Text('52','8','QUAD:' . $record->quad->name); 
		$pdf->Text('25','12','MAT:' . $record->material->name);
		$pdf->Text('52','12','CLASS:' . $record->classification->name); 	
		$pdf->Text('25','16','L.U.:' . $record->lsg_unit->name);
		$pdf->Text('52','16','FEAT:' . $record->feature); 
		$pdf->Text('25','20','CAT#:' . $record->catalog_id);
		$pdf->Text('52','20','RN:' . $record->station_index); 
		$pdf->Text('25','24',date('d-M-Y',$record->created));
		$pdf->Text('52','24','USER:' . $record->user->username); 
		$pdf->Output($filename);

		if (!$update_record) { 
			$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
			$uid = Dba::escape($record->uid); 
			$type = 'ticket'; 

			$sql = "INSERT INTO `media` (`filename`,`type`,`record`) VALUES ('$filename','$type','$uid')";
			$db_results = Dba::write($sql); 

			if (!$db_results) { 
				Event::error('Database','Unknown Database error inserting ticket'); 
			} 
		} // if new

		return true;  

	} // write_ticket 

  /**
   * write_media
   */
  private static function write_media($uid,$data,$filename,$description) { 

		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 

		$results = fwrite($handle,$data); 
    fclose($handle); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
		$uid = Dba::escape($uid); 
    $description = Dba::escape($description); 
    $user_id = Dba::escape(\UI\sess::$user->uid); 
    $sql = "INSERT INTO `media` (`filename`,`type`,`record`,`notes`,`user`) VALUES ('$filename','media','$uid','$description','$user_id')";
    $db_results = Dba::write($sql); 

    if (!$db_results) { 
      Event::error('Database','Unknown Database erro inserting media'); 
    }

    return true; 

  } // write_media

  /**
   * write_3dmodel
   * Write a 3dmodel file, whatever that is
   */
  private static function write_3dmodel($uid,$data,$filename,$description) { 

		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 

		$results = fwrite($handle,$data); 
    fclose($handle); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
		$uid = Dba::escape($uid); 
    $description = Dba::escape($description); 
    $user_id = Dba::escape(\UI\sess::$user->uid); 
    $sql = "INSERT INTO `media` (`filename`,`type`,`record`,`notes`,`user`) VALUES ('$filename','3dmodel','$uid','$description','$user_id')";
    $db_results = Dba::write($sql); 

    $media_uid = Dba::insert_id(); 

    Content::regenerate_3dmodel_thumb($media_uid); 

    if (!$db_results) { 
      Event::error('Database','Unknown Database error inserting media'); 
    }

    return true; 

  } // write_3dmodel

	/**
	 * delete
	 * Deletes some content from the FS
	 */
	public function delete() { 

		$results = $this->{'delete_'.$this->type}(); 	
		return $results; 

	} // delete

	/**
	 * delete_image
	 * Delete an image assoicated with a record
	 */
	private function delete_image() {

    if (file_exists($this->thumbnail)) { 
      $results = unlink($this->thumbnail); 
      if (!$results) {
        Event::error('Thumbnail','Unable to delete ' . $this->thumbnail);
        return false;
      }
    }

		$results = unlink($this->filename); 
		if (!$results AND file_exists($this->filename)) { 
			Event::error('general','Error unable to remove Media file'); 
			return false; 
		} 

    Event::record('Record Image','Record image ' . $this->filename . ' was deleted by ' . \UI\sess::$user->username); 

		$uid = Dba::escape($this->uid); 
		$sql = "DELETE FROM `image` WHERE `uid`='$uid' LIMIT 1"; 
		$db_results = Dba::write($sql); 	

		return true; 

	} // delete_image

	/**
	 * delete_qrcode
	 * Delete the qrcode for this record
	 */
	private function delete_qrcode() { 

		$results = unlink($this->filename); 
		if (!$results) { 
			Event::error('general','Error unable to remove QRCode'); 
			return false; 
		} 

		$uid = Dba::escape($this->uid); 
		$sql = "DELETE FROM `media` WHERE `uid`='$uid' AND `type`='qrcode'"; 
		$db_results = Dba::write($sql); 
	
		return true; 

	} // delete_qrcode

	/**
	 * delete_ticket
	 * This deletes the pdf ticket we generated
	 */
	private function delete_ticket() { 

		$results = unlink($this->filename); 
		if (!$results) { 
			Event::error('general','Error unable to remove PDF ticket'); 
			return false; 
		}
		
		$uid = Dba::escape($this->uid); 
		$sql = "DELETE FROM `media` WHERE `uid`='$uid' AND `type`='ticket'";
		$db_results = Dba::write($sql); 

		return true; 

	} // delete_ticket

  /**
   * delete_media
   */
  private function delete_3dmodel() { 

    // Remove the thumb first
    if (file_exists($this->thumbnail)) { 
      $results = unlink($this->thumbnail); 
      if (!$results) { 
        Event::error('general','Unable to remove ' . $this->thumbnail); 
        return false; 
      }
    } // if we have a thumbnail

    if (file_exists($this->filename)) { 
      $results = unlink($this->filename); 
      if (!$results) { 
        Event::error('general','Error unable to remove ' . $this->filename); 
        return false; 
      }
    } 

    $uid = Dba::escape($this->uid); 
    $sql = "DELETE FROM `media` WHERE `uid`='$uid' AND `type`='3dmodel'"; 
    $db_results = Dba::write($sql); 

    return true; 

  } // delete_3dmodel

  /**
   * delete_media
   */
  private function delete_media() { 

    $results = unlink($this->filename); 
    if (!$results) { 
      Event::error('general','Error unable to remove ' . $this->filename); 
      return false; 
    }

    $uid = Dba::escape($this->uid); 
    $sql = "DELETE FROM `media` WHERE `uid`='$uid' AND `type`='media'"; 
    $db_results = Dba::write($sql); 

    return true; 

  } // delete_media

	/**
	 * generate_filename
	 * Generates a filename based on the name and extension using the data_root defined
	 * in the config file
	 */
	public static function generate_filename($name,$extension='') { 

		// If we pass an extension prepend a dot
		$extension = $extension ? '.' . $extension : '';

		$filename = self::generate_directory() . '/' . escapeshellcmd($name) . '-' . date("dmHis",time()) . $extension; 

		return $filename; 

	} // genereate_filename

	// Make sure we've got the directories we need
	private static function generate_directory() { 

		$dir = false; 
		$directory = Config::get('data_root') . '/' . escapeshellcmd(Config::get('site')) . '/' . date('Y',time()) . '/' . date('m',time()); 
	
		if (!is_dir($directory)) { 
			$dir = mkdir($directory,0775,true); 
		} 
	
		if (!$dir AND !is_dir($directory)) { 
			// Throw an error? 
			Event::error('Content','Unable to build directory structure out of ' . Config::get('data_root')); 
			return false; 
		} 	

		return $directory; 

	} // generate_directory 

	// Figure out what file extension this should have based on its mime type
	private static function get_extension($mime_type) { 

		$data = explode("/", $mime_type);
		$extension = isset($data['1']) ? $data['1'] : null;

		if ($extension == 'jpeg') { $extension = 'jpg'; }

		return $extension;

	} // get_extension 

  /**
   * figure out the mime type
   */
  private static function get_mime($extension) { 

    switch ($extension) { 
      case 'stl':
        return 'application/sla';
      break;
      case 'ply':
        return 'application/octet-stream';
      break;
    }

    return '';

  } // get_mime

  /**
   * record
   * This returns the content of specified type for this record
   * return object is dependent on what's requested
   */
  public static function record($record_uid,$type) { 

    switch ($type) { 
      case '3dmodel':
        $retval = self::record_3dmodel($record_uid); 
      break;
      case 'media': 
        $retval = self::record_media($record_uid); 
      break;
      case 'image':
        $retval = self::record_image($record_uid); 
      break; 
    }

    return $retval; 

  } // record

  /**
   * level
   * Return the content for the specified level of specified type
   */
  public static function level($uid,$type) { 

    switch ($type) { 
      case 'image':
        $retval = self::level_image($uid); 
      break;
    }

    return $retval;

  } // level

  /**
   * record_3dmodel
   * This returns an array of the 3dmodels assoicated with the record
   */
  private static function record_3dmodel($record_uid) { 

    $record_uid = Dba::escape($record_uid); 
    $sql = "SELECT `uid` FROM `media` WHERE `record`='$record_uid' AND `type`='3dmodel' ORDER BY `uid`"; 
    $db_results = Dba::read($sql); 

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid']; 
    }

    self::build_cache($results,'media'); 

    return $results; 

  } // record_3dmodel

  /**
   * record_media
   * This returns an array of the media assoicated with the record
   */
  private static function record_media($record_uid) { 

    $record_uid = Dba::escape($record_uid); 
    $sql = "SELECT `uid` FROM `media` WHERE `record`='$record_uid' AND `type`='media' ORDER BY `uid`"; 
    $db_results = Dba::read($sql); 

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[] = $row['uid']; 
    }

    self::build_cache($results,'media'); 

    return $results; 

  } // record_media

  /**
   * record_image
   * This returns an array of the images assoicated with the record
   */
  private static function record_image($record_uid) { 

    $record_uid = Dba::escape($record_uid); 
    $sql = "SELECT * FROM `image` WHERE `record`='$record_uid' AND `type`='record' ORDER BY `uid`"; 
    $db_results = Dba::read($sql); 

    $results = array(); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('image',$row['uid'],$row); 
      $results[] = $row['uid']; 
    }
  
    return $results; 

  } // record_image

  /**
   * level_image
   * Returns an array of the images assoicated with the level
   */
  private static function level_image($uid) { 

    $uid = Dba::escape($uid);
    $sql = "SELECT * FROM `image` WHERE `record`='$uid' AND `type`='level' ORDER BY `uid`";
    $db_results = Dba::read($sql);

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('image',$row['uid'],$row);
      $results[] = $row['uid'];
    }

    return $results;

  } // level_image

  /**
   * upload
   * Handles uploading of media (http upload)
   */
  public static function upload($uid,$input,$source,$type) { 

    $retval = true; 

    // Figure out which one it is based on the file extension
    $info = pathinfo($source['media']['name']); 
    
    switch ($info['extension']) { 
      case 'jpg':
      case 'gif':
      case 'tiff':
      case 'png':
        $retval = self::upload_image($uid,$input,$source,$type); 
      break; 
      case 'stl':
      case 'ply':
        $retval = self::upload_3dmodel($uid,$input,$source); 
      break;
      default:
        $retval = self::upload_media($uid,$input,$source); 
      break; 
    } // end switch

    return $retval; 

  } // upload

  /**
   * upload_record
   * This handles uploading of an image for a record
   */
  private static function upload_image($uid,$post,$files,$type) { 

    if (!isset($files['media']['name'])) { 
      Error::add('upload','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Error::add('upload','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 

    $allowed_types = array('png','jpg','tiff','gif'); 
    if (!in_array(strtolower($path_info['extension']),$allowed_types)) { 
      Error::add('upload','Invalid file type, only png,jpg,tiff and gif are allowed'); 
      return false; 
    }

    // Read in source file
    $image_data = file_get_contents($files['media']['tmp_name']); 

    if (!$image_data) { 
      Error::add('upload','unable to read uploaded file, please try again'); 
      return false; 
    } 

    // We need the mime type now
    $mime = 'image/' . $path_info['extension'];

    // Write the thumbnail and record image to the filesystem, and insert into database
    $retval = Content::write($uid,'image',$image_data,$mime,$post['description'],$type); 

    Event::add('success','Image uploaded, thanks!','small'); 

    return $retval; 

  } // upload_record

  /**
   * upload_3dmodel
   * For uploading of 3dmodels, if it's a stl file we create a preview
   */
  private static function upload_3dmodel($uid,$post,$files) { 

    if (!isset($files['media']['name'])) { 
      Error::add('media','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Error::add('media','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 

    $allowed_types = array('ply','stl'); 
    if (!in_array(strtolower($path_info['extension']),$allowed_types)) { 
      Error::add('media','Invalid file type, only ply and stl are allowed'); 
      return false; 
    }

    // Read in source file
    $data = file_get_contents($files['media']['tmp_name']); 

    if (!$data) { 
      Error::add('media','unable to read uploaded file, please try again'); 
      return false; 
    } 

    $filename = Content::write($uid,'3dmodel',$data,$path_info['extension'],$post['description']); 

    Event::add('success','3d Model uploaded, thanks!','small'); 

    return true; 


  } // upload_3dmodel

  /**
   * upload_media
   * For uploading of misc media files
   */
  private static function upload_media($uid,$post,$files) { 

    if (!isset($files['media']['name'])) { 
      Error::add('media','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Error::add('media','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 

    // Read in source file
    $data = file_get_contents($files['media']['tmp_name']); 

    if (!$data) { 
      Error::add('media','unable to read uploaded file, please try again'); 
      return false; 
    } 

    $filename = Content::write($uid,'media',$data,$path_info['extension'],$post['description']); 

    Event::add('success','Media uploaded, thanks!','small'); 

    return true; 


  } // upload_media

  /**
   * update
   * Updates the content
   */
  public static function update($type,$uid,$input) { 

    switch ($type) { 
      case 'image':
        self::update_image($uid,$input); 
      break;
      case '3dmodel':
        self::update_3dmodel($uid,$input); 
      break;
    } // type

  } // update

  /**
   * update_image
   * This updates the information on a record
   */
  private static function update_image($uid,$input) { 

    $uid = Dba::escape($uid); 
    $notes = Dba::escape($input['description']); 

    $sql = "UPDATE `image` SET `notes`='$notes' WHERE `uid`='$uid' LIMIT 1"; 
    Dba::write($sql); 

  } // update_image

  /**
   * update_3dmodel
   * This updates the information on a 3dmodel, just the description
   */
  private static function update_3dmodel($uid,$input) { 

    $uid = Dba::escape($uid); 
    $notes = Dba::escape($input['description']); 

    $sql = "UPDATE `media` SET `notes`='$notes' WHERE `uid`='$uid' LIMIT 1"; 
    Dba::write($sql); 

  } // update_3dmodel

	/**
	 * regenerate_qrcodes
	 * Rebuilds all qrcodes, useful if the URL changes
	 */
	public static function regenerate_qrcodes() {

		// No timelimit!
		set_time_limit(0); 

		$sql = "SELECT `record`.`uid`,`media`.`filename` FROM `record` LEFT JOIN `media` ON `media`.`record`=`record`.`uid` AND `media`.`type`='qrcode'";
		$db_results = Dba::read($sql); 

		while ($row = Dba::fetch_assoc($db_results)) {
			// Overwrite the existing file, if it exists!
			Content::write($row['uid'],'qrcode',$row['filename']);
		}

		return true; 

	} // regenerate_qrcodes

  /**
   * regenerate_thumb
   * Rebuild thumbnails, can pass an option to rebuild all or just requested
   */
  public static function regenerate_thumb($image_uid='') { 

    // No timelimit 
    set_time_limit(0); 

    if ($image_uid) { 
      $records = array($image_uid); 
    }
    else {
      $sql = "SELECT * FROM `image`"; 
      $db_results = Dba::read($sql); 

      while ($row = Dba::fetch_assoc($db_results)) { 
        parent::add_to_cache('image',$row['uid'],$row); 
        $records[] = $row['uid']; 
      }
    }

    foreach ($records as $record_uid) { 
      $image = new Content($record_uid,'image');

      $image_data = $image->source();

      if (!$image_data) {
          // Something failed here
          Event::error('Thumb','Unable to read record image for ' . $image->filename);
          continue;
      }

      $data = Image::generate_thumb($image_data,array('height'=>120,'width'=>120),$image->extension);

      // Put it on the filesystem
  		$handle = fopen($image->thumbnail,'w');
	  	if (!$handle) {
  			Event::error('Content','Unable to write file - Permission denied');
        continue;
  		}
	  	$results = fwrite($handle,$data);

  		if (!$results) {
  			Event::error('Content','Unable to write Image to disk');
  		}

    } // end foreach

    return true; 

  } // regenerate_thumb

  /**
   * regenerate_3dmodel
   * regenerate the thumbnail for a 3dmodel, takes optional filename
   * to do just one
   */
  public static function regenerate_3dmodel_thumb($model_uid='') { 

    // No timelimit 
    set_time_limit(0); 

    if ($model_uid) { 
      $records = array($model_uid); 
    }
    else { 
      $sql = "SELECT * FROM `media` WHERE `type`='3dmodel'";
      $db_results = Dba::read($sql); 
      while ($row = Dba::fetch_assoc($db_results)) { 
        parent::add_to_cache('media',$row['uid'],$row); 
        $records[] = $row['uid']; 
      } 
    }

    foreach ($records as $model_uid) { 

      $model = new Content($model_uid,'3dmodel'); 

      $pov_filename = $model->filename . '.pov';
      $thumb_filename = substr($model->filename,0,strlen($model->filename)-3) . 'png'; 

      // Build a preview thumbnail
      $cmd = Config::get('stl2pov_cmd') . ' ' . $model->filename . ' > ' .  $pov_filename; 
      Event::error('STL2POV',$cmd); 
      exec($cmd); 
      $cmd = Config::get('megapov_cmd') . " +I$pov_filename +O$thumb_filename -D +P +W120 +H120 +A0.5";
      Event::error('MEGAPOV',$cmd); 
      exec($cmd); 

      unlink($pov_filename); 
    }

    return true; 

  } // regenerate_3dmodel_thumb

} // end content class
?>
