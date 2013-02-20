<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

# This handles the content file org crap
class content {

	public $uid; // UID of object
	public $filename; // "UID" for this class, the real filename
	public $mime; // Mime type of this image
	public $type; // Type of object, most likely an image for now
	public $source; // Raw data of the object
	private $valid_types = array('record','thumb','qrcode','ticket'); 

	public function __construct($uid='',$type) { 

		if (!in_array($type,$valid_types)) { 
			Error::add('general','Invalid Content Type Specified');
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

		$this->filename = $row['data']; 
		$this->mime = $row['type']; 

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

		$filename = Config::get('data_root') . '/qrcode';

	} // load_qrcode_data

	// Reads in and returns the source of this file
	public function source() { 

		$data = file_get_contents($this->filename); 
		$this->source = $data; 
		return $data; 	

	} // source

	// This writes out the specified data 
	public static function write($uid,$type,$data,$mime_type) { 

		$extension = self::get_extension($mime_type); 
		
		$record = new Record($uid); 

		// First we need to generate a filename /SITE/YYYY/RECORD-MMDDHHMMSS
		switch ($type) { 
			case 'thumb': 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension . '.thumb'); 
			break; 
			default: 
			case 'record': 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
			break; 
		} 
		
		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write filename ' . $filename); 
			return false; 
		} 
		fwrite($handle,$data); 

		// We don't add thumbnails to the db, this isn't right at all
		if ($type == 'thumb') { 
			return true; 
		} 
		
		$filename = Dba::escape($filename); 
		$uid = Dba::escape($uid); 
		$mime_type = Dba::escape($mime_type); 
		$sql = "INSERT INTO `image` (`data`,`record`,`type`) VALUES ('$filename','$uid','$mime_type')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Error::add('general','Error inserting record of Image into Database'); 
			return false; 
		} 

		return $db_results; 

	} // write

	/**
	 * delete
	 * Deletes some content from the FS
	 */
	public function delete() { 

		$results = $this->{'delete_'.$type}(); 	
		return $results; 

	} // delete

	/**
	 * delete_record
	 * Delete a record
	 */
	private function delete_record() {

		$results = unlink($this->filename); 
		if (!$results) { 
			Error::add('general','Error unable to remove Media file'); 
			return false; 
		} 
		$sql = "DELETE FROM `images`"; 

	} //delete_record

	/**
	 * generate_filename
	 * Generates a filename based on the name and extension using the data_root defined
	 * in the config file
	 */
	public static function generate_filename($name,$extension,$time='') { 

		// Allows us to pass in a date if we are adding old
		if (!$time) { $time=time(); }

		$filename = self::generate_directory() . '/' . escapeshellcmd($name) . '-' . date("dmHis",$time) . '.' . $extension; 

		return $filename; 

	} // genereate_filename

	// Make sure we've got the directories we need
	private static function generate_directory() { 

		$directory =  Config::get('data_root') . '/' . escapeshellcmd(Config::get('site')) . '/' . date("Y",time()); 
	
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
		$extension = $data['1'];

		if ($extension == 'jpeg') { $extension = 'jpg'; }

		return $extension;

	} // get_extension 

} // end content class
?>
