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
  public $record_type; // Type of record (level,krotovina,image)
  public $source; // Raw data of the object
  private $valid_types = array('image','qrcode','ticket','media','3dmodel','level','scatterplot','feature','krotovina'); 

  public function __construct($uid='',$type,$record_type) {

    if (!in_array($type,$this->valid_types)) {
      Event::error('general','Invalid Content Type Specified');
      return false;
    }

    $this->uid          = intval($uid);
    $this->type         = $type;
    $this->record_type  = $record_type;
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
      case 'ticket':
      case 'level':
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
    $this->__construct($this->uid,$this->type,$this->record_type); 

  } // refresh

  /**
   * _display
   * User friendly display
   */
  public function _display($variable) { 


  } // _display

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
      $sql = "SELECT * FROM `image` WHERE `uid`=?";
      $db_results = Dba::read($sql,array($uid));
      $row = Dba::fetch_assoc($db_results);
      if (!count($row)) { $retval = false; }
      parent::add_to_cache('image',$uid,$row); 
    }

    $info = pathinfo($row['data']); 

    $this->extension    = empty($info['extension']) ? '' : $info['extension'];
    $this->filename     = empty($row['data']) ? false : Config::get('data_root') . '/' . $row['data'];
    $this->thumbnail    = Config::get('data_root') . '/' . $row['data'] . '.thumb';
    $this->mime         = $row['mime'];
    $this->parentuid    = $row['record'];
    $this->notes        = $row['notes']; 
    $this->user         = $row['user']; 
    $this->type         = 'image';
    $this->record_type  = $row['type'];

		return $retval; 

	} // load_image_data

	/** 
	 * load_qrcode_data
	 * This loads the qrcode image from the record info
	 * UID is the record uid
	 */
	private function load_qrcode_data($uid) { 

		$sql = "SELECT * FROM `media` WHERE `record`=? AND `record_type`=? AND `type`='qrcode'";
		$db_results = Dba::read($sql,array($uid,$this->record_type)); 

		$row = Dba::fetch_assoc($db_results); 

		// We didn't find anything :(
		if (empty($row['uid'])) { return false; }

		$this->filename     = empty($row['filename']) ? false : Config::get('data_root') . '/' . $row['filename'];
		$this->uid	        = $row['uid'];
		$this->parentuid    = $row['record']; 
		$this->mime	        = 'image/png'; 
    $this->record_type  = $row['record_type'];
    $this->type         = 'qrcode';

		return true; 

	} // load_qrcode_data

  /**
   * load_scatterplot_data
   * This just fills out the filename, nothing else
   * UID is level.uid
   */
  private function load_scatterplot_data($uid) {

    switch ($this->record_type) {
      case 'feature':
        $feature = new Feature($uid);
        $base = Config::get('data_root') . '/' . $feature->site->name . '/plots/Feature-' . $feature->uid;
      break;
      default:
      case 'level':
        $level = new Level($uid);
        $base = Config::get('data_root') . '/' .  $level->site->name . '/plots/Level-'  .$level->uid;
      break;
    }

    if (!file_exists($base . '-3D.png')) { 
      return false;
    }

    $this->filename = array();
    $this->filename['3D'] = $base . '-3D.png';
    $this->filename['EstXElv'] = $base . '-EstXElv.png';
    $this->filename['EstXNor'] = $base . '-EstXNor.png';
    $this->filename['NorXElv'] = $base . '-NorXElv.png';

    return true; 

  } // load_scatterplot_data

	/**
	 * load_ticket_data
	 * This loads the ticket pdf info from media 
	 */
	private function load_ticket_data($uid) { 

		$sql = "SELECT * FROM `media` WHERE `record`=? AND `record_type`=? AND `type`='ticket'";
		$db_results = Dba::read($sql,array($uid,$this->record_type)); 

		$row = Dba::fetch_assoc($db_results); 

    if (!isset($row['uid'])) { return false; }

		$this->filename = Config::get('data_root') . '/' . $row['filename']; 
		$this->uid	= $row['uid']; 
		$this->parentuid = $row['record']; 
		$this->mime	= 'application/pdf'; 

		return true;  

	} // load_ticket_data

  /**
   * load_level_data
   * Loads up the level report pfd
   */
  private function load_level_data($uid) { 

    $sql = "SELECT * FROM `media` WHERE `record`=? AND `record_type`=? AND `type`='level'";
    $db_results = Dba::read($sql,array($uid,$this->record_type)); 

    $row = Dba::fetch_assoc($db_results); 

    if (!isset($row['uid'])) { return false; }

    $this->filename = Config::get('data_root') . '/' . $row['filename']; 
    $this->uid  = $row['uid']; 
    $this->parentuid = $row['record']; 
    $this->mime = 'application/pfd';

    return true; 

  } // load_level_data

  /**
   * load_feature_data
   * Loads up the feature report pfd
   */
  private function load_feature_data($uid) {

    $sql = "SELECT * FROM `media` WHERE `record`=? AND `record_type`=? AND `type`='feature'";
    $db_results = Dba::read($sql,array($uid,$this->record_type));
    
    $row = Dba::fetch_assoc($db_results);

    if (!isset($row['uid'])) { return false; }

    $this->filename = Config::get('data_root') . '/' . $row['filename'];
    $this->uid = $row['uid'];
    $this->parentuid = $row['record'];
    $this->mime = 'application/pfd';

    return true;

  } // load_feature_data

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
      $sql = "SELECT * FROM `media` WHERE `uid`=? AND `record_type`=? AND `type`='media'"; 
      $db_results = Dba::read($sql,array($uid,$this->record_type)); 
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
      $sql = "SELECT * FROM `media` WHERE `uid`=? AND `record_type`=? AND `type`='3dmodel'"; 
      $db_results = Dba::read($sql,array($uid,$this->record_type)); 
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
        $level = new Level($uid);
				$extension = self::get_extension($mime_type); 
        $filename = self::generate_filename($level->site->name . '-level-' . $level->record,$extension);
      break;
      case 'krotovina':
        $krotovina = new Krotovina($uid);
				$extension = self::get_extension($mime_type); 
        $filename = self::generate_filename($krotovina->site->name . '-krotovina-' . $krotovina->catalog_id,$extension);
      break;
      case 'feature':
        $feature = new Feature($uid);
				$extension = self::get_extension($mime_type); 
        $filename = self::generate_filename($feature->site->name . '-feature-' . $feature->catalog_id,$extension);
      break;
      case 'record':
      default:
    		$record = new Record($uid); 
				$extension = self::get_extension($mime_type); 
				$filename = self::generate_filename($record->site->name . '-' . $record->catalog_id,$extension); 
      break;
    }

		// First we need to generate a filename /SITE/YYYY/RECORD-MMDDHHMMSS
		switch ($type) { 
			case 'qrcode':
				// If data is passed, use that as filename
				$filename = !empty($data) ? $data : self::generate_filename($record->site->name . '-qrcode-' . $record->catalog_id,'png'); 
				$results = self::write_qrcode($uid,$filename,$data); 
			break; 
			case 'ticket': 
				// If data is passed, use that as filename
				$filename = !empty($data) ? $data : self::generate_filename($record->site->name . '-ticket-' . $record->catalog_id,'pdf');
				$results = self::write_ticket($record,$filename,$data); 
			break; 
      case 'feature':
        $filename = self::generate_filename($feature->site->name . '-feature-' . $feature->catalog_id . '-report','pdf');
        $results = self::write_feature($feature,$filename,$data);
      break;
      case 'level': 
        // If data is passed, use that as a filename
        $filename = !empty($data) ? $data : self::generate_filename($level->site->name . '-level-' . $level->unit->name . '-' . $level->quad->name . '-' . $level->record,'pdf');
        $results = self::write_level($level,$filename,$data); 
      break;
      case '3dmodel':
        $extension = $mime_type;
        $filename = empty($filename) ? self::generate_filename($record->site->name . '-' . $record->catalog_id,$extension) : $filename;
        $results = self::write_3dmodel($uid,$data,$filename,$options,$record_type); 
      break;
      case 'media': 
        $extension = $mime_type; 
        $filename = empty($filename) ? self::generate_filename($record->site->name . '-' . $record->catalog_id,$extension) : $filename;
        $results = self::write_media($uid,$data,$filename,$options,$record_type); 
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
	public static function write_qrcode($uid,$filename,$update_record) { 

    if (!is_writeable(Config::get('prefix') . '/lib/cache')) {
      Err::add('general','QRCode tmp directory unwriteable, unable to create QRCode for record ticket');
      return false;
    }

    if (empty($filename)) { 
      Err::add('general','QRCode generation failure');
      Event::error('Content::write_qrcode','No filename specified for UID:'. $uid);
      return false;
    }
    elseif (!is_writeable(dirname($filename))) {
      //Err::add('general','QRCode generation failure, Permission Denied');
      Event::error('Content::write_qrcode',dirname($filename) . ' is not writeable');
      return false;
    }

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
			$sql = "INSERT INTO `media` (`filename`,`type`,`record`,`user`,`record_type`) VALUES ('$filename','$type','$uid',?,?)"; 
			$db_results = Dba::write($sql,array(\UI\sess::$user->uid,'record')); 

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

    $type = \UI\sess::$user->site->get_setting('ticket');
    $record_type = 'record';

    //FIXME: BROKEN BROKEN BROKEN
    $pdf = new Genpdf($type);
    $pdf->{"ticket_$type"}($record,$filename);

		if (!$update_record) { 
			$filename = ltrim($filename,Config::get('data_root')); 
			$uid = $record->uid; 

			$sql = "INSERT INTO `media` (`filename`,`type`,`record`,`user`,`record_type`) VALUES (?,?,?,?,?)";
			$db_results = Dba::write($sql,array($filename,'ticket',$uid,\UI\sess::$user->uid,$record_type)); 

			if (!$db_results) { 
				Event::error('Database','Unknown Database error inserting ticket'); 
			} 
		} // if new

		return true;  

	} // write_ticket 

  /**
   * write_feature
   * Generate the feature report form
   */
  private static function write_feature(&$feature,$filename) {

    $pdf = new Genpdf('feature_report');
    $pdf->feature_report($feature,$filename);

    return true; 

  } // write_feature

  /**
   * write_level
   * Generate a level report form 
   */
  private static function write_level(&$level,$filename,$update_record) { 

    Err::clear();

    # We have to calc the length here
    $records = $level->records(); 
    $total_pages = ceil(3 + (count($records)/55)); 
    $current_page = 1; 
    $start_time = time();

    $pdf = new FPDF(); 
    $pdf->AddPage('P','A4'); 
    $pdf->SetFont('Times');
    $pdf->SetFontSize('10'); 
    $pdf->Text('200','295',$current_page. '/' . $total_pages); 
    $pdf->Text('140','295'," Generated " . date("Y-M-d H:i",$start_time));
    $pdf->Text('3','295',$level->site->name . ' ' . $level->record . ' FORM');

    // Return the primary image
    $levelimage = new Content($level->image,'image'); 

    # Make sure the levelimage is readable, throw nasty error if not
    if (!is_readable($levelimage->filename)) { 
      Event::error('Level-PDF','Level Image ' . $levelimage->filename . ' is not readbale');
      Err::add('level_image','Level Image is not readable or not found');
    }
    if (!is_writeable(Config::get('prefix') . '/lib/cache') OR !is_readable(Config::get('prefix') . '/lib/cache')) {
      Event::error('Level-PDF','Cache directory unwriteable, unable to resize image');
      Err::add('level_image','Cache directory unwriteable, unable to resize level image');
    }

    if (Err::occurred()) { 
      Err::display('level_image');
      require \UI\template('/footer');
      exit;
    }


    $ex_one = new User($level->excavator_one);
    $ex_two = new User($level->excavator_two);
    $ex_three = new User($level->excavator_three);
    $ex_four  = new User($level->excavator_four);

    $excavator_list = $ex_one->name . ', ' . $ex_two->name . ', ' . $ex_three->name . ', ' . $ex_four->name;
    $excavator_list = rtrim($excavator_list,', '); 
    $close_user = new User($level->closed_user);

    # Metadata Information
    $pdf->SetTitle('UNIT:' . $level->unit->name . ' QUAD:' . $level->quad->name . ' LEVEL:' . $level->record);
    $pdf->SetSubject('EXCAVATION LEVEL FORM'); 
    $pdf->SetKeywords(date('d-M-Y',$level->created) . ' ' . $level->quad->name . ' ' . $level->unit->name . ' ' . $level->record . ' ' . $level->site->name); 

    
    # Default font settings
    $pdf->SetFont('Times','B'); 
		$pdf->SetFontSize('12'); 

    # Header
		$pdf->Text('3','13',$level->site->name . ' EXCAVATION LEVEL FORM');
    $pdf->Text('3','18',$level->site->description); 
    $pdf->Text('169','13','Started: ' . date('d-M-Y',$level->created)); 
    $pdf->Text('169','18','Closed: ' . date('d-M-Y',$level->closed_date)); 
    $pdf->Line('0','20','220','20');

    # Left side information
    $pdf->SetFontSize('15'); 
    $pdf->Text('5','25','INFORMATION');
    $pdf->SetFontSize('12');
    $pdf->Rect('5','27','94','49');
    $pdf->Line('41','27','41','76');
    $pdf->Text('7','32','UNIT'); 
    $pdf->Text('43','32',$level->unit->name);
    $pdf->Line('5','33','99','33');
    $pdf->Text('7','37','QUAD');
    $pdf->Text('43','37',$level->quad->name); 
    $pdf->Line('5','39','99','39');
    $pdf->Text('7','43',"LEVEL");
    $pdf->Text('43','43',$level->record);
    $pdf->Line('5','45','99','45');
    $pdf->Text('7','49','L.U.');
    $pdf->Text('43','49',$level->lsg_unit->name);
    $pdf->Line('5','51','99','51');
    $pdf->Text('7','55','EXCAVATOR #1'); 
    $pdf->Text('43','55',$ex_one->name); 
    $pdf->Line('5','57','99','57');
    $pdf->Text('7','62','EXCAVATOR #2');
    $pdf->Text('43','62',$ex_two->name);
    $pdf->Line('5','64','99','64'); 
    $pdf->Text('7','68','EXCAVATOR #3');
    $pdf->Text('43','68',$ex_three->name);
    $pdf->Line('5','70','99','70'); 
    $pdf->Text('7','74','EXCAVATOR #4');
    $pdf->Text('43','74',$ex_four->name); 

    # Right side elevations
    $pdf->SetFontSize('15'); 
    $pdf->Text('103','25','LOCUS');
    $pdf->SetFontSize('12');
    $pdf->Rect('103','27','58','49');
    $pdf->Line('128','27','128','76'); 
    $pdf->Line('144','27','144','64'); 
    $pdf->Text('130','32','Start'); 
    $pdf->Text('145','32','Finish'); 
    $pdf->Line('103','33','161','33'); 
    $pdf->Text('111','37','NW'); 
    $pdf->Text('129','37',$level->elv_nw_start); 
    $pdf->Text('145','37',$level->elv_nw_finish); 
    $pdf->Line('103','39','161','39'); 
    $pdf->Text('111','43','NE'); 
    $pdf->Text('129','43',$level->elv_ne_start); 
    $pdf->Text('145','43',$level->elv_ne_finish); 
    $pdf->Line('103','45','161','45'); 
    $pdf->Text('111','49','SW'); 
    $pdf->Text('129','49',$level->elv_sw_start); 
    $pdf->Text('145','49',$level->elv_sw_finish); 
    $pdf->Line('103','51','161','51'); 
    $pdf->Text('111','55','SE');
    $pdf->Text('129','55',$level->elv_se_start); 
    $pdf->Text('145','55',$level->elv_se_finish); 
    $pdf->Line('103','57','161','57'); 
    $pdf->Text('111','62','CN');
    $pdf->Text('129','62',$level->elv_center_start);
    $pdf->Text('145','62',$level->elv_center_finish);
    $pdf->Line('103','64','161','64');
    $pdf->Text('104','68','NORTHING');
    $pdf->Text('129','68',$level->northing);
    $pdf->Line('103','70','161','70');
    $pdf->Text('104','74','EASTING');
    $pdf->Text('129','74',$level->easting);


    # Image scale is 2.83, so primary level image needs to be
    # 190 * 2.83 by 155 * 2.83 or 538 (width) by 439 height (aspect ratio of 1.22:1)
    # Images will be resized to 980x803

    # Primary Image
    $resized_file = Config::get('prefix') . '/' . \UI\resize($levelimage->filename,array('w'=>'980','h'=>'803','canvas-color'=>'#ffffff'));
    $pdf->Image($resized_file,'10','87','190','155');

    # Footer
    $pdf->SetFontSize('24');
    $pdf->Text('52','270','Unit ' . $level->unit->name . ' ' . $level->quad->name . ' - Level ' . $level->record . ' - LU ' . $level->lsg_unit->name); 

    # This might kersplode, but try to build the scatterplots for this level
    # On the fly
    $plotcmd = Config::get('prefix') . '/bin/build-scatter-plots ' . escapeshellarg($level->uid);
    $output = exec($plotcmd);

    # Get the scatterplot info for this level
    $plot = new Content($level->uid,'scatterplot');
    
      # Page 2, grids
    $pdf->AddPage(); 
    $pdf->SetFontSize('18');
    $pdf->SetFont('Times');
    $current_page++; 
		
    $pdf->Text('80','13','Mapped Objects');
    $legend_filename = Config::get('prefix') . '/images/archie_legend.png';

    # Make sure we have all 4 plots
    if (count($plot->filename) == 4) {


      $pdf->image($plot->filename['EstXNor'],'2','15','104','104');
      $pdf->image($plot->filename['EstXElv'],'105','15','104','104');
      $pdf->image($plot->filename['NorXElv'],'2','125','104','104');
      $pdf->image($plot->filename['3D'],'105','125','104','104');


    } // end if 4 files found
    // Tell em its empty
    else {

      $pdf->SetFontSize('25');
      $pdf->Text('35','135','No scatterplots available for this level');
      $pdf->SetFontSize('10');
    
    } 

    $pdf->Image($legend_filename,'60','240','100','27');
    $pdf->SetFontSize('10');
    $pdf->Text('200','295',$current_page. '/' . $total_pages); 
    $pdf->Text('140','295'," Generated " . date("Y-M-d H:i",$start_time));
    $pdf->Text('3','295',$level->site->name . ' ' . $level->record . ' FORM');

    # Page 3, questions
    $pdf->AddPage(); 
    $pdf->SetFontSize('10');
    $pdf->SetFont('Times');
    $current_page++; 
    $pdf->Text('200','295',$current_page. '/' . $total_pages); 
    $pdf->Text('140','295'," Generated " . date("Y-M-d H:i",$start_time));
    $pdf->Text('3','295',$level->site->name . ' ' . $level->record . ' FORM');

    # Long Answers
    $pdf->SetFontSize('13');
    $pdf->SetFont('Times','B');
    $pdf->Text('5','14','Describe: Sediment, Artifacts, Krotovina, Features'); 
    $pdf->Line('2','15','205','15');
    $pdf->SetFontSize('12');
    $pdf->SetFont('Times');
    $pdf->SetX('0');
    $pdf->SetY('16');
    $pdf->Write('4',$level->description);

    # Figure out how chatty they were and start from there
    $start_y = $pdf->GetY();

    $pdf->SetFontSize('13');
    $pdf->SetFont('Times','B');
    $pdf->Text('5',$start_y+12,'Describe the differences and similarities compared to the last level');
    $pdf->Line('2',$start_y+13,'205',$start_y+13);
    $pdf->SetFontSize('12');
    $pdf->SetFont('Times');
    $pdf->SetX('0');
    $pdf->SetY($start_y+14);
    $pdf->Write('4',$level->difference);

    # Figure out how chatty they were and start from there again
    $start_y = $pdf->GetY();

    $pdf->SetFontSize('13'); 
    $pdf->SetFont('Times','B');
    $pdf->Text('5',$start_y+12,'Did you find anything interesting or significant?'); 
    $pdf->Line('2',$start_y+13,'205',$start_y+13);
    $pdf->SetFontSize('12');
    $pdf->SetFont('Times');
    $pdf->SetX('0');
    $pdf->SetY($start_y+14);
    $pdf->Write('4',$level->notes);
    $pdf->SetFontSize('10');


    # Records (list)
    while (count($records)) { 

      $pdf->AddPage(); 
      $current_page++; 
      $pdf->Text('200','295',$current_page. '/' . $total_pages); 
      $pdf->Text('140','295'," Generated " . date("Y-M-d H:i",$start_time));
      $pdf->Text('3','295',$level->site->name . ' ' . $level->record . ' FORM');
      $pdf->SetFontSize('13');
      $pdf->SetFont('Times','B'); 
      $pdf->Text(3,'9','Records'); 
      $pdf->SetFontSize('10');

      $row = 0;
      $line_count = 0; 
      $start_y = 20;
      $record_count = count($records);

      foreach ($records as $record_id) { 
        # If we've reached the end, trim and reset
        if ($line_count == 55) { 
          $start_y = 20; 
          $records = array_slice($records,55);
          break; 
        }

        $line_count++;  
        # First and 59th (2nd row) lines and we set the table
        if ($line_count == 1) { 
          $pdf->SetFont('Times','B'); 
          $pdf->Line(2,'10',202,'10'); 
          $pdf->Text(5,14,'Catalog');
          $pdf->Text(25,14,'RN');
          $pdf->Text(44,14,'Material');
          $pdf->Text(68,14,'Classification'); 
          $pdf->Text(102,14,'Northing');
          $pdf->Text(119,14,'Easting');
          $pdf->Text(135,14,'Elevation');
          $pdf->Text(155,14,'Quanity');
          $pdf->Text(179,14,'Entered By');
          $pdf->Line(2,'16',202,'16');

          # Itterate through the records
          $pdf->SetFontSize('10');
          $pdf->SetFont('Times');

          $line_end = ($record_count > 55) ? (55*5)+16 : ($record_count*5)+16;
          $pdf->Line(2,'10',2,$line_end);
          $pdf->Line(21,'10',21,$line_end); 
          $pdf->Line(40,'10',40,$line_end);
          $pdf->Line(66,'10',66,$line_end);
          $pdf->Line(101,'10',101,$line_end);
          $pdf->Line(118,10,118,$line_end);
          $pdf->Line(133,10,133,$line_end);
          $pdf->Line(153,10,153,$line_end);
          $pdf->Line(177,10,177,$line_end);
          $pdf->Line(202,10,202,$line_end);
        } 

        # Load and print record record
        $record = new Record($record_id); 
        $pdf->Text(3,$start_y,$record->catalog_id);
        $pdf->Text(22,$start_y,$record->station_index);
        $pdf->Text(41,$start_y,$record->material->name); 
        $pdf->Text(67,$start_y,$record->classification->name);
        $pdf->Text(102,$start_y,$record->northing);
        $pdf->Text(119,$start_y,$record->easting);
        $pdf->Text(134,$start_y,$record->elevation);
        $pdf->Text(154,$start_y,$record->quanity);
        $pdf->Text(178,$start_y,$record->user->username);
        $pdf->Line(2,$start_y+1,202 ,$start_y+1);
        $start_y += 5;

      } // end foreach
      if ($line_count < 55) { break; }

    } // end while records
/*    
    $pdf->Text('5','235','Krotovina'); 
    $pdf->Line('2','236','205','236'); 

    $pdf->Text('5','245','Features'); 
    $pdf->Line('2','246','205','246');
*/  

    ob_end_clean(); 
    $pdf->Output(); 

  } // write_level

  /**
   * write_media
   */
  private static function write_media($uid,$data,$filename,$description,$record_type) { 

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

		$filename = ltrim($filename,Config::get('data_root')); 
    $sql = "INSERT INTO `media` (`filename`,`type`,`record`,`notes`,`user`,`record_type`) VALUES (?,?,?,?,?,?)";
    $db_results = Dba::write($sql,array($filename,'media',$uid,$description,\UI\sess::$user->uid,$record_type)); 

    if (!$db_results) { 
      Event::error('Database','Database error inserting media, please check error log'); 
    }

    return true; 

  } // write_media

  /**
   * write_3dmodel
   * Write a 3dmodel file, whatever that is
   */
  private static function write_3dmodel($uid,$data,$filename,$description,$record_type) { 

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

    // Strip the data_root off of the filename
		$filename = ltrim($filename,Config::get('data_root')); 

    $sql = "INSERT INTO `media` (`filename`,`type`,`record`,`notes`,`user`,`record_type`) VALUES (?,?,?,?,?,?)";
    $db_results = Dba::write($sql,array($filename,'3dmodel',$uid,$description,\UI\sess::$user->uid,$record_type)); 

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

		$sql = "DELETE FROM `image` WHERE `uid`=? LIMIT 1"; 
		$db_results = Dba::write($sql,array($this->uid)); 	

		return true; 

	} // delete_image

	/**
	 * delete_qrcode
	 * Delete the qrcode for this record
	 */
	private function delete_qrcode() { 

    if (file_exists($this->filename)) { 
  		$results = unlink($this->filename); 
  		if (!$results) { 
  			Event::error('general','Error unable to remove QRCode'); 
  			return false; 
  		} 
    } // if the file is even there
    else {
      Event::record('warning','QRCode file - ' . $this->filename . ' was not found when attempting to delete it, this may indicate a problem with your data');
    }

    // Do the deleting
		$sql = "DELETE FROM `media` WHERE `uid`=? AND `type`='qrcode'"; 
		$db_results = Dba::write($sql,array($this->uid)); 
	
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
		
		$sql = "DELETE FROM `media` WHERE `uid`=? AND `type`='ticket'";
		$db_results = Dba::write($sql,array($this->uid)); 

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
    $sql = "DELETE FROM `media` WHERE `uid`=? AND `type`='3dmodel'"; 
    $db_results = Dba::write($sql,array($this->uid)); 

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

    $sql = "DELETE FROM `media` WHERE `uid`=? AND `type`='media'"; 
    $db_results = Dba::write($sql,array($this->uid)); 

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

	} // generate_filename

	// Make sure we've got the directories we need
	private static function generate_directory() { 

		$dir = false; 
		$directory = Config::get('data_root') . '/' . escapeshellcmd(\UI\sess::$user->site->name) . '/' . date('Y',time()) . '/' . date('m',time()); 
	
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

    if (!strpos($mime_type,'/')) { 
      $extension = $mime_type; 
    }
    else { 
  		$data = explode("/", $mime_type);
  		$extension = isset($data['1']) ? $data['1'] : null;
    }
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
      case 'media': 
        $retval = self::get_media($record_uid,$type,'record'); 
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

    $retval = array();

    switch ($type) { 
      case 'image':
        $retval = self::level_image($uid); 
      break;
      case 'media':
      case '3dmodel':
        $retval = self::get_media($uid,$type,'level');
      break;
    }

    return $retval;

  } // level

  /**
   * krotovina
   * Return the content for the specified krotovina
   */
  public static function krotovina($uid,$type) { 

    $retval = array();

    switch ($type) { 
      case 'image':
        $retval = self::get_image($uid,'krotovina');
      break;
      case 'media':
      case '3dmodel':
        $retval = self::get_media($uid,$type,'krotovina');
      break;
    }

    return $retval;

  } // krotovina

  /**
   * feature
   * Return the content for the specified krotovina
   */
  public static function feature($uid,$type) { 

    $retval = array();

    switch ($type) { 
      case 'image':
        $retval = self::get_image($uid,'feature');
      break;
      case 'media':
      case '3dmodel':
        $retval = self::get_media($uid,$type,'feature');
      break;
    }

    return $retval;

  } // feature;


  /**
   * get_media
   * Return an array of media of type assoicated with the $record_type
   */
  private static function get_media($record_uid,$type,$record_type) {

    $sql = "SELECT `uid` FROM `media` WHERE `record`=? AND `record_type`=? AND `type`=? ORDER BY `uid`";
    $db_results = Dba::read($sql,array($record_uid,$record_type,$type));

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid'];
    }

    self::build_cache($results,'media');

    return $results;

  } // get_3dmodels

  /**
   * record_3dmodel
   * This returns an array of the 3dmodels assoicated with the record
   */
  private static function record_3dmodel($record_uid) { 

    $record_uid = Dba::escape($record_uid); 
    $sql = "SELECT `uid` FROM `media` WHERE `record`='$record_uid' AND `record_type`='record' AND `type`='3dmodel' ORDER BY `uid`"; 
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
   * get_image
   * Return an image of the specified type and uid
   */
  private static function get_image($uid,$type) { 

    $sql = "SELECT * FROM `image` WHERE `record`=? AND `type`=? ORDER BY `uid`";
    $db_results = Dba::read($sql,array($uid,$type));

    $results = array();

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('image',$row['uid'],$row);
      $results[] = $row['uid'];
    }

    return $results; 

  } // get_image

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

    // Lowercase that stuff
    $info['extension'] = strtolower($info['extension']); 

    switch ($info['extension']) { 
      case 'jpg':
      case 'gif':
      case 'tiff':
      case 'png':
        $retval = self::upload_image($uid,$input,$source,$type); 
      break; 
      case 'stl':
      case 'ply':
        $retval = self::upload_3dmodel($uid,$input,$source,$type); 
      break;
      default:
        $retval = self::upload_media($uid,$input,$source,$type); 
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
      Err::add('upload','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Err::add('upload','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 
    $path_info['extension'] = strtolower($path_info['extension']); 

    $allowed_types = array('png','jpg','tiff','gif'); 
    if (!in_array($path_info['extension'],$allowed_types)) { 
      Err::add('upload','Invalid file type, only png,jpg,tiff and gif are allowed'); 
      return false; 
    }

    // Make sure it's not too smal
    $image_info = getimagesize($files['media']['tmp_name']);

    if ($image_info['0'] < 640 OR $image_info['1'] < 480) { 
      Event::add('warning','Uploaded Image is less than 640x480. Thumbnails and reports may not work correctly with this image');
    }

    // Read in source file
    $image_data = file_get_contents($files['media']['tmp_name']); 

    if (!$image_data) { 
      Err::add('upload','unable to read uploaded file, please try again'); 
      return false; 
    } 

    // We need the mime type now
    $mime = 'image/' . $path_info['extension'];

    // Write the thumbnail and record image to the filesystem, and insert into database
    $retval = Content::write($uid,'image',$image_data,$mime,$post['description'],$type); 

    if ($retval) { 
      Event::add('success','Image uploaded, thanks!','small'); 
    }
    else {
      Err::add('upload','Error uploading image, please contact your administrator');
    }

    return $retval; 

  } // upload_record

  /**
   * upload_3dmodel
   * For uploading of 3dmodels, if it's a stl file we create a preview
   */
  private static function upload_3dmodel($uid,$post,$files,$type) { 

    if (!isset($files['media']['name'])) { 
      Err::add('media','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Err::add('media','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 

    $allowed_types = array('ply','stl'); 
    if (!in_array(strtolower($path_info['extension']),$allowed_types)) { 
      Err::add('media','Invalid file type, only ply and stl are allowed'); 
      return false; 
    }

    // Read in source file
    $data = file_get_contents($files['media']['tmp_name']); 

    if (!$data) { 
      Err::add('media','unable to read uploaded file, please try again'); 
      return false; 
    } 

    $filename = Content::write($uid,'3dmodel',$data,$path_info['extension'],$post['description'],$type); 

    Event::add('success','3d Model uploaded, thanks!','small'); 

    return true; 


  } // upload_3dmodel

  /**
   * upload_media
   * For uploading of misc media files
   */
  private static function upload_media($uid,$post,$files,$type) { 

    if (!isset($files['media']['name'])) { 
      Err::add('media','No file found, please select a file to upload');
      return false; 
    } 

    if (empty($files['media']['tmp_name'])) { 
      Err::add('media','Upload failed, please try again'); 
      return false; 
    } 

    $path_info = pathinfo($files['media']['name']); 

    // Read in source file
    $data = file_get_contents($files['media']['tmp_name']); 

    if (!$data) { 
      Err::add('media','unable to read uploaded file, please try again'); 
      return false; 
    } 

    $filename = Content::write($uid,'media',$data,$path_info['extension'],$post['description'],$type); 

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
      $row['filename'] = file_exists($row['filename']) ? $row['filename'] : false;
      // Delete the old entry if the file is no longer there
      if (!$row['filename']) { 
        $qrcode = new Content($row['uid'],'qrcode');
        $qrcode->delete(); 
      }
  		Content::write($row['uid'],'qrcode',$row['filename']);
		}

		return true; 

	} // regenerate_qrcodes

  /**
   * regenerate_ticket
   * Rebuild the tickets
   */
  public static function regenerate_ticket() {

    set_time_limit(0);

    $sql = "SELECT `record`.`uid`,`media`.`filename` FROM `record` LEFT JOIN `media` ON `media`.`record`=`record`.`uid` AND `media`.`record_type`='record' AND `media`.`type`='ticket'";
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      Content::write($row['uid'],'ticket',$row['filename']);
    }

    return true;

  } // regenerate_ticket

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
