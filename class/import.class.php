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
    while (($data = fgetcsv($handle)) !== false) { 
      if (count($data) != 5) { 
        Error::add('import','Invalid CSV format on line:' . $line); 
        return false; 
      }

      // Look for this line 
      $station_index = Dba::escape($data['0']); 
      $sql = "SELECT `uid`,`northing`,`easting`,`elevation` FROM `record` WHERE `station_index`='$station_index'";
      $db_results = Dba::read($sql); 

      if (!$row = Dba::fetch_assoc($db_results)) { 
        $warning_lines++; 
        $missing .= ', ' . $line; 
        Event::record('import','FAIL: unable to find RN:' . $station_index . ' northing,easting and elevation not imported'); 
      }
      else { 
        // Check to make sure we've got floatvals
        $is_valid = true; 
        $is_valid = (settype($data['1'],'float') == $data['1']) ? $is_valid : false;
        $is_valid = (settype($data['2'],'float') == $data['2']) ? $is_valid : false; 
        $is_valid = (settype($data['3'],'float') == $data['3']) ? $is_valid : false; 
        if ($is_valid) { 
          // Check to see if xyz are set already - don't add if so
          if ($row['northing'] == 0 AND $row['easting'] == 0 AND $row['elevation'] == 0) {
            $valid[] = $data;
          }
          else {
            $existing_lines++; 
          }
        }
        else { 
          $error_lines++; 
          $invalid .= ', ' . $line; 
        }
      }
      $line++; 
    }

    fclose($handle); 

    if ($warning_lines > 0) { 
      Error::warning('general',$warning_lines . ' invalid lines found'); 
      Error::warning('station_index','Lines:' . ltrim($missing,',')); 
    } 

    if ($error_lines > 0) { 
      Error::add('general',$error_lines . ' invalid lines found. Aborting, nothing imported'); 
      if (strlen($invalid)) { Error::add('Coordinate Value','Lines:' . ltrim($invalid,',')); }
      return false; 
    }

    // We made it this far update!
    foreach ($valid as $row) { 
      
      $station_index = Dba::escape($row['0']); 
      $northing = Dba::escape($row['1']); 
      $easting = Dba::escape($row['2']); 
      $elevation = Dba::escape($row['3']); 
      $notes = Dba::escape($row['4']); 

      $sql = "UPDATE `record` SET `northing`='$northing', `easting`='$easting', `elevation`='$elevation', `notes`=IFNULL(CONCAT(`notes`,'$notes'),'$notes') WHERE `station_index`='$station_index' LIMIT 1";
      $db_results = Dba::write($sql); 

      Event::record('Import',"Northing:$northing Easting:$easting Elevation:$elevation Notes:$notes set on RN:$station_index"); 

    }

    Event::add('success','Imported:' . count($valid) . ' lines, Skipped:' . $existing_lines . ' lines','small'); 

    return true; 

  } // run_xyz_station

}
?>
