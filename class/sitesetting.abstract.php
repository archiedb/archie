<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

abstract class sitesetting { 

	public $name; 
  public static $values = array();

  /**
   * _print
   * Output the value
   */
  public function _print($key) { 

    if (!isset($this->$key)) { return false; }

    echo scrub_out($this->$key);

  } // _print

} // sitesetting
?>
