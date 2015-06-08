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
      case 'northing':
      case 'easting':
      case 'elevation':
        // Must be int and rounded to 3 places and something entered
        if ((!is_numeric($value) OR round($value,3) != $value) AND strlen($field) ) {
          $retval = false;
        }
      break;
      case 'rn':
        // Must be Int
        if (!is_numeric($value) ) {
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
