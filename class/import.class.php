<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Import {

  private static $data_dir; // Path to our data

  public $type; 

  /**
   * constructor
   */
  public function __construct($type) { 

    // Make sure we support this format & type
    if (!$this->has_type($type)) { 
      Event::error('IMPORT','Error unknown import type:' . $type); 
      return false; 
    }

    $this->type = $type;  

  } // constructor

  /**
   * has_type
   */
  private function has_type($type) { 

    $methods = get_class_methods('Import'); 

    $func_name = 'run_' . $type;
    if (in_array($func_name,$methods)) { 
      return true; 
    }

    return false; 

  } // has_type

  /**
   * auto_init
   */
  public static function _auto_init() { 

    self::$data_dir = Config::get('data_root') . '/imports';
    if (!is_dir(self::$data_dir)) { 
      $retval = mkdir(self::$data_dir,0755,true); 
    }

  } // _auto_init

  /**
   * data_filename
   * The data for the report
   */
  private function data_filename() { 

    $filename = self::$data_dir . '/' . $this->type . '-' . date('M-d-Y',time()) . '-' . \UI\sess::$user->username . '.data'; 

    return $filename; 

  } // data_filename

  /**
   * run
   * This is the wrapper for import runs
   */
  public function run($filename) { 

    if (!is_readable($filename)) { 
      Error::add('import','Unable to read import file:' . $filename); 
      return false; 
    }

    switch ($this->type) { 
      case 'xyz_station':
        $retval = $this->run_xyz_station($filename);
      break;
    }

    return $retval; 

  } // run

  /**
   * run_xyz_station
   * Run the xyz station import 
   * assume csv file contents
   */
  private function run_xyz_station($csv_file) { 

    // These values only apply to the current site
    $site = \UI\sess::$user->site->uid;
  
    if (($handle = fopen($csv_file, "r")) === false) {
        Error::add('general','Unable to open uploaded file'); 
        return false;
    }

    // Itterate though this csv
    $line = 1; 
    $error_lines=0;
    $existing_lines=0;
    $warning_lines=0; 
    $missing = '';
    $invalid = ''; 
    $found = array();
    while (($data = fgetcsv($handle)) !== false) { 
      if (count($data) != 5) { 
        Error::add('import','Invalid CSV format on line:' . $line); 
        return false; 
      }

      // Look for this line 
      $station_index = $data['0']; 
      $sql = "SELECT * FROM `spatial_data` WHERE `station_index`=?";
      $db_results = Dba::read($sql,array($station_index));

      $indexes = array();

      while ($row = Dba::fetch_assoc($db_results)) {
        $indexes[] = $row;
      }

      if (count($indexes) == 0) { 
        $warning_lines++; 
        $missing .= ', ' .  $station_index; 
        Event::record('import','FAIL: unable to find RN:' . $station_index . ' northing,easting and elevation not imported'); 
        continue;
      }

      // Check to make sure we've got floatvals
      $is_valid = true; 
      $is_valid = (settype($data['1'],'float') == $data['1']) ? $is_valid : false;
      $is_valid = (settype($data['2'],'float') == $data['2']) ? $is_valid : false; 
      $is_valid = (settype($data['3'],'float') == $data['3']) ? $is_valid : false; 
        
      // Run through the records found and see if any are in this site, if not BAD 
      foreach ($indexes as $key=>$row) {

        $sql = "SELECT * FROM `" . $row['record_type'] . "` WHERE `uid`=? AND `site`=?";
        $db_results = Dba::read($sql,array($row['record'],$site));
        $record = Dba::fetch_assoc($db_results);

        if (!isset($record['uid'])) { 
          $warning_lines++;
          $missing .= ', ' . $station_index;
          Event::record('import','Warning: ' . $station_index . ' not found on a record');
        }
        else {
          if (isset($found[$station_index])) { 
            $error_lines++;
            $invalid .= ', ' . $station_index;
            Event::record('import','FAIL: RN ' . $station_index . ' is on more than one record -- ' . json_encode($indexes));
          }
          $found[$station_index] = true;
        }

        if ($is_valid) { 
          // This is only valid if the northing/easting/elevation are not set
          if ($row['northing'] == 0 AND $row['easting'] == 0 AND $row['elevation'] == 0) {
            $valid[$row['uid']] = $data;
          }
          else {
            $existing_lines++; 
          }
        }
        // Else the data isn't valid
        else { 
          $error_lines++; 
          Event::add('import','Datum Invalid for RN:' . $station_index . ' N:' . $data['1'] . ' E:' . $data['2'] . ' Ev:' . $data['3']);
          $invalid .= ', ' . $station_index; 
        }

      } // foreach spatialdata records

    $line++; 
  } // end while check

  fclose($handle); 

    if ($warning_lines > 0) { 
      Error::warning('general',$warning_lines . ' invalid lines found'); 
      Error::warning('station_index','Station Indexes:' . ltrim($missing,',')); 
    } 

    if ($error_lines > 0) { 
      Error::add('general',$error_lines . ' invalid lines found. Aborting, nothing imported'); 
      if (strlen($invalid)) { Error::add('Coordinate Value','Station Indexes:' . ltrim($invalid,',')); }
      return false; 
    }

    // We made it this far update!
    foreach ($valid as $uid=>$row) { 
      
      $notes = Dba::escape("\n--Station Import--\n" . $row['4'] . "\n--End Station Import--\n"); 

      $sql = "UPDATE `spatial_data` SET `northing`=?, `easting`=?, `elevation`=?, `note`=IFNULL(CONCAT(`note`,'$notes'),'$notes') WHERE `uid`=? LIMIT 1";
      $db_results = Dba::write($sql,array($row['1'],$row['2'],$row['3'],$uid)); 

      Event::record('Import',"Northing:" . $row['1'] . " Easting:" . $row['2'] . " Elevation:" . $row['3'] . " Notes:$notes set on RN:" . $row['0']); 

    }

    Event::add('success','Imported:' . count($valid) . ' lines, Skipped:' . $existing_lines . ' lines','small'); 

    return true; 

  } // run_xyz_station

}
?>
