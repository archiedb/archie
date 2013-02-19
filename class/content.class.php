<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

# This handles the content file org crap
class content {

	public $uid; // UID of object
	public $filename; // "UID" for this class, the real filename
	public $mime; // Mime type of this image
	public $type; // Type of object, most likely an image for now
	public $source; // Raw data of the object


	public function __construct($uid,$type) { 

		//FIXME: Hack to get things rolling
		$this->uid = $uid; 
		$this->type = $type; 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `image` WHERE `uid`='$uid'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		$this->filename = $row['data']; 
		$this->mime = $row['type']; 

		if ($type == 'thumb') { 
			$this->filename = $this->filename . '.thumb'; 
		} 

	} // construct

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
		
		// Now record this in the database
		//FIXME: This should be in the record class, or somewhere else good enough for now
		$filename = Dba::escape($filename); 
		$uid = Dba::escape($uid); 
		$mime_type = Dba::escape($mime_type); 
		$sql = "INSERT INTO `image` (`data`,`record`,`type`) VALUES ('$filename','$uid','$mime_type')"; 
		$db_results = Dba::write($sql); 

		return $db_results; 

	} // write

	/**
	 * delete
	 * Deletes some content from the FS
	 */
	public static function delete($uid,$filename,$type) { 



	} // delete

	/**
	 * generate_filename
	 * Generates a filename based on the name and extension using the data_root defined
	 * in the config file
	 */
	public static function generate_filename($name,$extension) { 

		$filename = self::generate_directory() . '/' . escapeshellcmd($name) . '-' . date("dmHis",time()) . '.' . $extension; 

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
