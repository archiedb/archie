<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
// This is just a class with a bunch of data functions
// used for the reports

class TestData { 

  private function __clone() {}
  private function __construct() {}

  /**
   * Return the data of $type and the valid or invalid
   * data based on the second params
   */
  public static function values($type,$valid,$option) { 

    // Function names
    $valid_str = $valid ? 'invalid' : 'valid';
    $function = $type . '_' . $valid_str;

    $methods = get_class_methods('TestData'); 
    
    $data = array(); 
    
    // If the function exists, call it
    if (in_array($function,$methods)) { 
      $data = TestData::{$function}($option); 
    }

    return $data; 

  } // populate

  /**
   * record_invalid
   * Return invalid data, if option passed only
   * that field should be invalid
   */
  private static function record_invalid($option) { 



  } // record_invalid

  /**
   * record_valid
   * Return all valid data!
   */
  private static function record_valid($option) { 

    $input['unit'] = 'A'; 
    $input['level'] = '49'; 
    $input['lsg_unit'] = '49'; 
    $input['station_index'] = '1'; 
    $input['northing'] = '133.132';
    $input['easting'] = '133.132';
    $input['elevation'] = '134.321';
    $input['xrf_matrix_index'] = '1541'; 
    $input['weight'] = '123'; 
    $input['width'] = '123'; 
    $input['thickness'] = '123';
    $input['quanity'] = '1'; 
    $input['xrf_artifact_index'] = '1234'; 
    $input['material'] = '10'; 
    $input['classification'] = '11'; 
    $input['notes'] = 'ZZZ';
    $input['quad'] = '4'; 
    $input['user'] = '6'; 
    
    return $input; 

  } // record_valid

} 

?>
