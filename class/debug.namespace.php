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
 * check_uploads
 * Make sure that uploads are going to work
 */
function check_uploads() {
  
  $file_uploads = ini_get('file_uploads') ? true : false;

  return $file_uploads;

} // check_uploads

/**
 * check_upload_size
 * Make sure that the size is reasonable
 */
function check_upload_size() { 

  // We're going to call 20M reasonable
  $upload_max = ini_get('upload_max_filesize');
  $post_max = ini_get('post_max_size');

  if (substr($upload_max,0,-1) < 20 OR substr($post_max,0,-1) < 20) {
    return false;
  }

  return true;

} // check_upload_size

/**
 * return_upload_size
 * Return the smaller of the two limits on the upload size
 */
function return_upload_size() { 

  $upload_max = ini_get('upload_max_filesize');
  $post_max = ini_get('post_max_size');

  if (substr($post_max,0,-1) < substr($upload_max,0,-1)) { 
    return $post_max;
  }

  return $upload_max;

} // return_upload_size

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

/**
 * 3dmodel_to_png
 * Checks that we've got the required commands to convert stl -> png
 */
function model_to_png() { 

  if (!is_executable(\Config::get('stl2pov_cmd'))) { return false; }
  if (!is_executable(\Config::get('megapov_cmd'))) { return false; }

  return true; 

} // 3dmodel_to_png

?>
