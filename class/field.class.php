<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Field { 

	// Constructor takes a uid
	public function __construct() { 

		return true; 

	} // constructor

  /**
   * validate
   * Takes a field type and an input value and returns true/false
   * if the value is valid for the current field type
   */
  public static function validate($field,$value) { 

    $retval = true;

    switch ($field) {
      // INT, rounded to 3 places or blank and less than 99,999.999 (DECIMAL 8,3)
      case 'weight':
      case 'height':
      case 'width':
      case 'thickness':
      case 'northing':
      case 'easting':
      case 'elevation':
        // These fields can't be more then 99999.999
        if ($value > 99999.999) {
          $retval = false;
          break;
        }
        if (!strlen($value)) { 
          $retval = true;
          break;
        }
        // Allow the fields to be 0, just not lower
        if (!is_numeric($value) OR round($value,3) != $value OR $value < 0) {
          $retval = false;
        }
      break;
      // INT, greater than 0
      case 'xrf_artifact_index':
      case 'quanity':
      case 'xrf_matrix_index':
      case 'catalog_id':
      case 'station_index':
        if (!is_numeric($value) OR $value <= 0) {
          $retval = false;
        }
      break;
      default:
        $retval = false;
      break;
    }

    return $retval;

  } // validate

  /**
   * validforfilename
   * A-Z,a-z,0-9,-,_
   * This may end up on the FS, so restrict it
   */
  public static function validforfilename($input) { 

    if (preg_match('/[^a-z_\-0-9]/i',$input)) {
      return false;
    }

    return true; 

  } // validforfilename

  /**
   * notempty
   * Make sure the field is not empty
   * Whitespace doesn't count
   */
  public static function notempty($input) { 

    if (!strlen(trim($input))) { 
      return false; 
    }

    return true; 

  } // notempty

} // end class field
?>
