<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 


/**
 * Content 
 * This class deals with "media" assoicated with a record
 * it does both the write and read
 */
class content {

  public $uid; // UID of object
  public $filename; // "UID" for this class, the real filename
  public $mime; // Mime type of this image
  public $parentuid; // Parent UID this is assoicated with
  public $type; // Type of object, most likely an image for now
  public $source; // Raw data of the object
  private $valid_types = array('record','thumb','qrcode','ticket'); 

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
	 * load_record_data
	 * This loads a record image from its UID
	 */
	private function load_record_data($uid) {

    $uid = Dba::escape($uid);
    $sql = "SELECT * FROM `image` WHERE `uid`='$uid'";
    $db_results = Dba::read($sql);

    $row = Dba::fetch_assoc($db_results);

    $this->filename = Config::get('data_root') . '/' . $row['data'];
    $this->mime = $row['type'];
    $this->parentuid = $row['record'];

		return $db_results; 

	} // load_record_data

	/** 
	 * load_thumb_data
	 * Loads the thumbnail data for the specified image
	 */
	public function load_thumb_data($uid) { 

		$this->load_record_data($uid); 
		$this->filename = $this->filename . '.thumb'; 

		return true; 

	} // load_thumb_data 

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
		if (!count($row)) { return false; }

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

		$this->filename = Config::get('data_root') . '/' . $row['filename']; 
		$this->uid	= $row['uid']; 
		$this->parentuid = $row['record']; 
		$this->mime	= 'application/pdf'; 

		return true;  

	} // load_ticket_data

	// Reads in and returns the source of this file
	public function source() { 

		$data = file_get_contents($this->filename); 
		$this->source = $data; 
		return $data; 	

	} // source

	// This writes out the specified data 
	public static function write($uid,$type,$data='',$mime_type='',$options='') { 
		
		$record = new Record($uid); 

		// First we need to generate a filename /SITE/YYYY/RECORD-MMDDHHMMSS
		switch ($type) { 
			case 'thumb': 
				$extension = self::get_extension($mime_type); 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension . '.thumb'); 
				$results = self::write_thumb($data,$filename); 
			break; 
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
			default: 
			case 'record': 
				$extension = self::get_extension($mime_type); 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
				$results = self::write_record($uid,$data,$filename,$mime_type,$options); 
			break; 
		} 

		return $results; 

	} // write

	/**
	 * write a record image
	 */
	private static function write_record($uid,$data,$filename,$mime_type,$notes) {

		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 
		$results = fwrite($handle,$data); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		$filename = Dba::escape(ltrim($filename,Config::get('data_root'))); 
		$uid = Dba::escape($uid); 
		$mime_type = Dba::escape($mime_type); 
    $notes = Dba::escape($notes); 
    $user = Dba::escape(\UI\sess::$user->uid); 
		$sql = "INSERT INTO `image` (`data`,`record`,`type`,`user`,`notes`) VALUES ('$filename','$uid','$mime_type','$user','$notes')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Event:error('general','Error inserting record of Image into Database'); 
			return false; 
		} 

		return $db_results; 

	} // write_record

	/**
	 * write_thumb
	 * Write out the thumbnail image of the "image" attached to a record
	 */
	private static function write_thumb($data,$filename) { 
		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 
		$results = fwrite($handle,$data); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		return $results; 

	} // write_thumb

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
	 * delete
	 * Deletes some content from the FS
	 */
	public function delete() { 

		$results = $this->{'delete_'.$this->type}(); 	
		return $results; 

	} // delete

	/**
	 * delete_record
	 * Delete a record
	 */
	private function delete_record() {

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

	} //delete_record

	/**
	 * delete_thumb
	 * Delete the thumbnail of this record
	 */
	private function delete_thumb() { 

		$results = unlink($this->filename); 
		if (!$results) { 
			Event::error('general','Error unable to remove Media Thumbnail'); 
			return false; 
		} 

		return true; 

	} // delete_thumb

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
		$directory = Config::get('data_root') . '/' . escapeshellcmd(Config::get('site')) . '/' . date("Y",time()); 
	
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
   * upload
   * Handles uploading of media (http upload)
   */
  public static function upload($type,$uid,$input,$source) { 

    $retval = true; 

    switch ($type) { 
      case 'record': 
        $retval = self::upload_record($uid,$input,$source); 
      break; 
      default: 
        $retval = false; 
      break; 
    } // end switch

    return $retval; 

  } // upload

  /**
   * upload_record
   * This handles uploading of an image for a record
   */
  private static function upload_record($uid,$post,$files) { 

    if (!isset($files['image']['name'])) { 
      Error::add('upload','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['image']['tmp_name'])) { 
      Error::add('upload','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['image']['name']); 

    $allowed_types = array('png','jpg','tiff','gif'); 
    if (!in_array(strtolower($path_info['extension']),$allowed_types)) { 
      Error::add('upload','Invalid file type, only png,jpg,tiff and gif are allowed'); 
      return false; 
    }

    // Read in source file
    $image_data = file_get_contents($files['image']['tmp_name']); 

    if (!$image_data) { 
      Error::add('upload','unable to read uploaded file, please try again'); 
      return false; 
    } 

    // We need the mime type now
    $mime = 'image/' . $path_info['extension'];

    // Make a thumbnail
    $thumb = Image::generate_thumb($image_data,array('height'=>120,'width'=>120),$path_info['extension']);

    if (!$thumb) { 
      Error::add('upload','Unable to create thumbnail of uploaded image, make sure it is a valid image'); 
      return false; 
    }

    // Write the thumbnail and record image to the filesystem, and insert into database
    Content::write($uid,'thumb',$thumb,$mime); 
    Content::write($uid,'record',$image_data,$mime,$post['description']); 

    Event::add('success','Image uploaded, thanks!','small'); 

    return true; 

  } // upload_record

  /**
   * update
   * Updates the content
   */
  public static function update($type,$uid,$input) { 

    switch ($type) { 
      case 'record': 
        self::update_record($uid,$input); 
      break;
    } // type

  } // update

  /**
   * update_record
   * This updates the information on a record
   */
  private static function update_record($uid,$input) { 

    $uid = Dba::escape($uid); 
    $notes = Dba::escape($input['description']); 

    $sql = "UPDATE `image` SET `notes`='$notes' WHERE `uid`='$uid' LIMIT 1"; 
    Dba::write($sql); 

  } // update_record

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

} // end content class
?>
