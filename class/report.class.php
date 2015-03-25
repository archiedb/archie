<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Report {

  private static $data_dir; // Path to our data

  public $format; // CSV,Excell,??? (what data format)
  public $type; // All,Site,User,??? (what data)

  /**
   * constructor
   */
  public function __construct($format,$type) { 

    // Make sure we support this format & type
    if (!$this->has_format($format) OR !$this->has_type($format,$type)) { 
      Event::error('REPORT','Error unknown report type ' . $format . ':' . $type); 
      return false; 
    }

    $this->format = $format; 
    $this->type = $type;  

  } // constructor

  /**
   * has_format
   */
  private function has_format($format) { 

    $methods = get_class_methods('Report'); 

    if (in_array($format,$methods)) { 
      return true; 
    } 

    return false; 

  } // has_format 

  /**
   * has_type
   */
  private function has_type($format,$type) { 

    $methods = get_class_methods('Report'); 

    $func_name = $format . '_' . $type;
    if (in_array($func_name,$methods)) { 
      return true; 
    }

    return false; 

  } // has_type

  /**
   * auto_init
   */
  public static function _auto_init() { 

    self::$data_dir = Config::get('data_root') . '/reports';
    if (!is_dir(self::$data_dir)) { 
      $retval = mkdir(self::$data_dir,0755,true); 
    }

  } // _auto_init

  /**
   * download
   * Does the header mojo need to download the file in question
   */
  public function download($option='') { 

    // Clear the area
    ob_end_clean(); 
    ob_implicit_flush(true); 
    header("Content-Transfer-Encoding: binary"); 
    header("Cache-control: public"); 

    $filename = $this->data_filename(); 
    $date = date("dmY-hms",filemtime($filename));

    switch ($this->format) { 
      case 'csv': 
        header('Content-Type: application/vnd.ms-excel'); 
        header("Content-Disposition: attachment; filename=\"archie-export-$date.csv\""); 
        header('Content-Length: ' . filesize($filename)); 
      break; 
    }

    echo file_get_contents($filename); 
    exit; 

  } // download

  /**
   * request
   * create the file that'll tell us we're requesting a report
   */
  public function request($option='') { 

    $filename = $this->request_filename(); 

    // Option \n EMAIL
    $data = $option . "\n" . \UI\sess::$user->email;

    $retval = (file_put_contents($filename,$data) === false) ? false : true; 

    if (!$retval) { 
      Error::add('general','Unable to create report request'); 
    } 

    return $retval; 

  } // request

  /**
   * current_requests 
   * return array of filenames of the current requests
   */
  public static function current_requests() { 

    $results = array(); 

    if (is_dir(self::$data_dir)) { 
      if ($dh = opendir(self::$data_dir)) { 
        while (($file = readdir($dh)) !== false) {
          $info = pathinfo($file); 
          if ($info['extension'] == 'request') { 
              $results[] = self::$data_dir . '/' . $file; 
          }
        }
      }
    }

    return $results; 

  } // current_requests

  /**
   * request_filename
   * Just generate the filename we want
   */
  private function request_filename() { 

    $filename = self::$data_dir . '/' . $this->format . '_' . $this->type . '.request'; 

    return $filename; 

  } // request_filename

  /**
   * data_filename
   * The data for the report
   */
  private function data_filename() { 

    $filename = self::$data_dir . '/' . $this->format . '_' . $this->type . '.data'; 

    return $filename; 

  } // data_filename

  /**
   * last_filename
   * filename for when we are done
   */
  private function last_filename() { 

    $filename = self::$data_dir . '/' . $this->format . '_' . $this->type . '.last'; 

    return $filename; 

  } // last_filename; 

  /**
   * last_report
   * return the date of the last report
   */
  public function last_report() { 

    $filename = $this->last_filename(); 

    return filemtime($filename); 

  } // last_report

  /**
   * state
   * This returns a string of the current state of this request
   * a DATE is returned if it's been generated
   */
  public function state() { 


    // See if there's a current request
    $filename = $this->request_filename(); 
    
    if (file_exists($filename)) { 
      return '<span class="label label-info">Updating...</span>';
    }

    $filename = $this->last_filename(); 

    if (!file_exists($filename)) { 
        return '<span class="label label-important">Never</span>'; 
    }
    else {
        $class = "label-success"; 
        // Make sure it's not stale
        if ($this->is_stale()) { 
          $class = "label-warning";
        }
        return '<span class="label ' . $class . '">' .date('d-M-Y H:i:s',filemtime($filename)) . '</span>';
    }

    return '<span class="label label-inverse">Unknown</span>';

  } // state

  /**
   * is_stale
   * Based on the type of report determine if it is stale
   */
  public function is_stale() { 

    $function_name = $this->format . '_' . $this->type . '_stale'; 

    $retval = $this->{$function_name}(); 

    return $retval; 

  } // is_stale

  /**
   * csv_siterecord_stale
   */
  public function csv_siterecord_stale() { 

    $date = $this->last_report(); 

    $date = Dba::escape($date); 

    $sql = "SELECT `record`.`uid` FROM `record` WHERE `created`>='$date' OR `updated`>='$date'";
    $db_results = Dba::read($sql); 

    $rows = Dba::num_rows($db_results); 

    return $rows; 

  } // csv_site_stale

  /**
   * csv_sitefeature_stale
   */
  public function csv_sitefeature_stale() {

    $date = $this->last_report();

    $sql = "SELECT `feature`.`uid` FROM `feature` WHERE `created`>=? OR `update`>=?";
    $db_results = Dba::read($sql,array($date,$date));

    $rows = Dba::num_rows($db_results);

    return $rows;

  } // csv_sitefeature_stale

  /**
   * csv_sitekrotovina_stale
   */
  public function csv_sitekrotovina_stale() {

    $date = $this->last_report();

    $sql = "SELECT `krotovina`.`uid` FROM `krotovina` WHERE `created`>=? OR `update`>=?";
    $db_results = Dba::read($sql,array($date,$date));

    $rows = Dba::num_rows($db_results);

    return $rows;

  } // csv_sitekrotovina_stale

  /**
   * generate
   * This generates a report of the type and format specified
   */
  public function generate($options) { 

    // Run the function
    $retval = $this->{$this->format}($options); 

    // If it worked, touch the last-built file
    if ($retval) { 
      touch($this->last_filename()); 
    }

    return $retval; 

  } // generate

  /**
   * csv
   * This creates a csv report, of specified type (calls other functions)
   */  
  private function csv($option='') { 

    switch ($this->type) { 
      case 'sitefeature':
        $data = $this->csv_sitefeature($option);
      break;
      case 'sitekrotovina':
        $data = $this->csv_sitekrotovina($option);
      break;
      case 'siterecord':
      case 'site': 
        $data = $this->csv_siterecord($option); 
      break;
    }

    $filename = $this->data_filename(); 

    // Open a new filehandle
    $handle = fopen($filename,'w');

    foreach ($data as $row) {
      $retval = fputcsv($handle,$row) ? $retval : false;
    }

    fclose($handle);

    return $retval; 

  } // csv

  /**
   * csv_site
   * Create a csv for the specified site's records
   */
  private function csv_siterecord($site) { 

    // If they passed the UID
    if (is_numeric($site)) { $site = new site($site); }
    // Else assume they must have passed the name
    else { 
      $site_uid = Site::get_from_name($site);
      $site = new Site($site_uid); 
    }

    // If we still can't find the site, run away
    if (!$site->uid) { return false; }

    $site_uid = Dba::escape($site->uid);
    $sql = "SELECT `record`.`uid` FROM `record` WHERE `site`='$site_uid'";
    $db_results = Dba::read($sql);

    while ($row = Dba::fetch_assoc($db_results)) {
      $results[] = $row['uid']; 
    }

    // Cache it!
    Record::build_cache($results); 

    $data = array();

    // The header
    $data[] = array('site','catalog id','unit','level','litho unit','station index','xrf matrix index','weight','height','width','thickness','quantity','material','classification','quad','feature','krotovina','notes','created','northing','easting','elevation','user');

    foreach ($results as $record_uid) {
      $record = new Record($record_uid); 
      $record->notes = str_replace(array("\r\n", "\n", "\r"),' ',$record->notes);

      $data[] = array($site->name,$record->catalog_id,$record->level->unit,$record->level->record,$record->lsg_unit->name,
        $record->station_index,$record->xrf_matrix_index,$record->weight,$record->height,$record->width,$record->thickness,$record->quanity,
        $record->material->name,trim($record->classification->name),$record->level->quad->name,$record->feature->record,$record->krotovina->record,
        $record->notes,date("m-d-Y h:i:s",$record->created),$record->northing,$record->easting,$record->elevation,$record->user->username);
     } // end foreach 

    return $data; 

  } // csv_siterecord

  /**
   * csv_sitefeature
   * CSV of features of specified site
   */
  public function csv_sitefeature($site) { 

    // If they passed the UID
    if (is_numeric($site)) { $site = new site($site); }
    // Else assume they must have passed the name
    else { 
      $site_uid = Site::get_from_name($site);
      $site = new Site($site_uid); 
    }

    // If we still can't find the site, run away
    if (!$site->uid) { return false; }
    
    $sql = "SELECT `uid` FROM `feature` WHERE `site`=?";
    $db_results = Dba::read($sql,array($site->uid));

    $data = array();

    $data[] = array('site','catalog id','keywords','description','created','user');

    while ($row = Dba::fetch_assoc($db_results)) {
      $feature = new Feature($row['uid']);
      $data[] = array($site->name,$feature->catalog_id,$feature->keywords,$feature->description,date("m-d-Y h:i:s",$feature->created),$feature->user->name);
    }

    return $data;

  } // csv_sitefeature

  /**
   * csv_sitekrotovina
   * CSV of krotovina of specified site
   */
  public function csv_sitekrotovina($site) { 

    // If they passed the UID
    if (is_numeric($site)) { $site = new site($site); }
    // Else assume they must have passed the name
    else { 
      $site_uid = Site::get_from_name($site);
      $site = new Site($site_uid); 
    }

    // If we still can't find the site, run away
    if (!$site->uid) { return false; }

    $sql = "SELECT `uid` FROM `krotovina` WHERE `site`=?";
    $db_results = Dba::read($sql,array($site->uid));

    $data = array();

    $data[] = array('site','catalog id','keywords','description','created','user');

    while ($row = Dba::fetch_assoc($db_results)) { 
      $krotovina = new Krotovina($row['uid']);
      $data[] = array($site->name,$krotovina->catalog_id,$krotovina->keywords,$krotovina->description,date("m-d-Y h:i:s",$krotovina->created),$krotovina->user->name);
    } // end krotos

    return $data;

  } // csv_sitekrotovina

  /**
   * csv_sitelevel
   */
  public function csv_sitelevel($site) { 

    // If they passed the UID
    if (is_numeric($site)) { $site = new site($site); }
    // Else assume they must have passed the name
    else { 
      $site_uid = Site::get_from_name($site);
      $site = new Site($site_uid); 
    }

    // If we still can't find the site, run away
    if (!$site->uid) { return false; }

    $data = array();

    $data[] = array('site','catalog id','record','unit','quad','lsg unit','northing','easting','elv nw start','elv nw finish','elv ne start','elv ne finish','elv sw start','elv se finish','elv sw start','elv sw finish','elv center start','elv center finish','excavator one','excavator two','excavator three','excavator four','description','difference','notes','created','user','closed','closed date','closed user');


    return $data;

  } // csv_sitelevel

}
?>
