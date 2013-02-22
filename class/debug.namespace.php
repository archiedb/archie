<?php namespace debug;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * check_gd_support
 * Figures out if we've got the right phpgd support
 */
function check_gd_support() { 

  if (!function_exists('ImageCreateFromString')) {
    return false; 
  }

  $info = gd_info(); 

  // Make sure we have JPEG and PNG support
  if (!$info['PNG Support']) {
    Event::error('PNG Support','PHP-GD Does not support creation of PNGs'); 
    return false; 
  }
  if (!$info['JPEG Support']) {
    Event::error('JPEG Support','PHP-GD does not support creation of JPEGs'); 
    return false;
  }

  return true; 

} // check_gd_support

/** 
 * check_time_limit
 * Make sure we can change the time limit, if it's already zero that's good enough!
 */
 function check_time_limit() { 

  set_time_limit(0);
  $override = ini_get('max_execution_time') ? false : true;
  
  return $override; 

 } // check_time_limit

 /**
  * check_qrcode_cache_writeable
  * Make sure that the qrcode cache is writeable
  */
function check_qrcode_cache_writeable() {

  $dir = dirname(__FILE__); 
  $prefix = realpath($dir . "/../"); 
  $filename = $prefix . '/lib/phpqrcode/cache';

  if (!is_writeable($filename)) { return false; }

  return true; 

} // check_qrcode_cache_writeable

?>
