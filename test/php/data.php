<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
// This is just a class with a bunch of data functions
// used for the reports

class TestData { 

  private function __clone() {}
  private function __construct() {}

  /**
   * record
   * Return data of specified field, either valid or invalid
   */
  public static function record($field,$valid=true) { 

    switch ($field) { 
      case 'unit':
        $value = $valid ? array('F') : array(null,'1'); 
      break;
      case 'level':
        $value = $valid ? array(null,6) : array('A','51',-21);
      break;
      case 'lsg_unit':
        $value = $valid ? array(null,5) : array('ZZZ','213',-21); 
      break;
      case 'station_index':
        $value = $valid ? array(null,312) : array('ZZZ',-23);
      break;
      case 'weight':
      case 'height':
      case 'width':
        $value = $valid ? array(null,111.111) : array(-1.23411,-111); 
      break;
      case 'easting':
      case 'elevation':
      case 'northing':
        $value = $valid ? array(null,111.111) : array('-1203.123'); 
      break;
      case 'xrf_matrix_index':
        $value = $valid ? array(null,111) : array('ZZZ'); 
      break;
      case 'quanity':
        $value = $valid ? array(null,111) : array('ZZZ'); 
      break;
      case 'notes':
        $value = $valid ? array(null,111) : array(); 
      break;
    }

    return $value;

  } // record

  public static function level($field,$valid=true) {


    return array();

  } // level

} 

?>
