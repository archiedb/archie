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
    return false; 
  }
  if (!$info['JPEG Support']) {
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
function check_cache_writeable() {

  $dir = dirname(__FILE__); 
  $prefix = realpath($dir . "/../"); 
  $filename = $prefix . '/lib/cache';

  if (!is_writeable($filename)) { return false; }

  return true; 

} // check_qrcode_cache_writeable

/**
 * check_root_writeable
 * Make sure the root dir is writeable (for installer)
 */
function check_root_writeable() {

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");
  
  if (!is_writeable($prefix)) { return false; }

  return true;

}

/**
 * check_config_writeable
 * Make sure the config dir is writeable (for installer)
 */
function check_config_writeable() {

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");

  if (!is_writeable($prefix . '/config')) { return false; }

  return true; 

} // check_config_writeable

/**
 * 3dmodel_to_png
 * Checks that we've got the required commands to convert stl -> png
 */
function model_to_png($stl2pov,$megapov) { 

  if (!is_executable($stl2pov)) { return false; }
  if (!is_executable($megapov)) { return false; }

  return true; 

} // 3dmodel_to_png

/**
 * python_scatterplots
 * Checks that python and the needed modules are installed
 */
function check_python_scatterplots() { 

    $retval = true; 

    if (!is_executable('/usr/bin/python')) { 
      return false; 
    }

    $modules = array('MySQLdb','os','errno','csv','sys','numpy','matplotlib','ConfigParser');

    foreach ($modules as $module) { 

      $cmd = "/usr/bin/python -c 'import $module'";
      exec($cmd,$out,$return);
      // Just 0 check doesn't work matplotlib returns 1 sometimes even though its ok... :(
      if ($return !== 0 AND !empty($out)) { 
        $retval = false;
      }

    } // foreach python modules

    return $retval;


} // check_python_scatterplots

/** 
 * check_php_pdo
 * make sure that php-pdo is enabled
 */
function check_php_pdo() { 

  return extension_loaded('PDO');

} // check_php_pdo

/**
 * check_php_pdomysql
 * Check and make sure that the mysql part of pdo is loaded
 */
function check_php_pdomysql() { 

  return extension_loaded('pdo_mysql');

} // check_php_pdomysql

/**
 * check_archie_config
 * Returns true if the archie config is readable
 */
function check_archie_config_readable() {

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");

  return is_readable($prefix . '/config/settings.php');

}

/**
 * check_archie_config
 * Returns true if the archie config has min required values
 */
function check_archie_config() {

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");
  $data = parse_ini_file($prefix . '/config/settings.php'); 
  $results = '';

  $required = array('log_path','data_root','database_username','database_password','database_hostname','database_name','session_name','remember_length','session_length','session_cookielife','session_cookiesecure');

  foreach ($required as $field) { 
    if (!isset($data[$field])) {
      $results .= $field . ',';
    }
  }

  return rtrim($results,',');

} // check_archie_config

/**
 * check_mysql_config
 * Reads the config and tries to connect to mysql
 */
function check_mysql_config() { 

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");
  if (!file_exists($prefix . '/config/settings.php')) { return 'False'; }
  $data = parse_ini_file($prefix . '/config/settings.php'); 
  $dsn = 'mysql:host=' . $data['database_hostname'];
  try {
    $dbh = new \PDO($dsn,$data['database_username'],$data['database_password']);
  }
  catch (PDOException $e) {
    return 'DBError:' . $e->getMessage();
  }

  return '';

} // check_msyql_config

/**
 * check_mysql_inserted
 * Make sure the db exists and is inserted
 */
function check_mysql_db() { 

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");
  if (!file_exists($prefix . '/config/settings.php')) { return 'False'; }
  $data = parse_ini_file($prefix . '/config/settings.php'); 
  $dsn = 'mysql:host=' . $data['database_hostname'];
  try {
    $dbh = new \PDO($dsn,$data['database_username'],$data['database_password']);
  }
  catch (PDOException $e) {
    return 'DBError:' . $e->getMessage();
  }

  // Now try to select the DB
  if ($dbh->exec('USE `' . $data['database_name'] . '`') === false) {
    return 'Unable to select DB :::' . print_r($dbh->errorInfo(),1);
  }

  // Check for user table with a row
  $handle = $dbh->query('SELECT * FROM `users`');
  if (($data = $handle->fetch(\PDO::FETCH_NUM)) === false) {
    return 'No Users detected, DB insert failure';
  }

  return '';

} // check_msyql_db

/**
 * check_imagemagick
 * make sure convert exists
 */
function check_imagemagick() {

    return is_executable('/usr/bin/convert');

} // check_imagemagick

/**
 * check_mod_rewrite
 * check for rewrites
 */
function check_mod_rewrite() { 

  if (function_exists('apache_get_modules')) {
    if (in_array('mod_rewrite',apache_get_modules())) {
      return true;
    }
  }

  return false;

} // check_mod_rewrite


?>
