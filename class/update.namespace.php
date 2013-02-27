<?php namespace Update;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/** 
 * Check for updates
 */
function check () { 



} 

/**
 * Run the update(s)
 */
function run () { 


} 

/**
 * Returns true if code is "up to date"
 * 
 */
function check_version() {

  $git_version = intval(get_gitorious_version()); 
  $this_version = intval(get_working_version()); 

  if ($this_version < $git_version) { return false; }

  return true; 

}

/**
 * check_db_version
 * Checks to see if the db is up to date
 */
function check_db_version() { 

  $working_version = intval(get_working_db_version()); 
  $build_version = 1;

  if ($working_version < $build_version) { return false; }

  return true; 

} // check_db_version

/**
 * get_working_version
 * Returns the version of software we are running
 */
function get_working_version() { 

  return file_get_contents(\Config::get('prefix') . '/docs/BUILD');

} // get_working_version

/**
 * get_working_db_version
 */
function get_working_db_version() { 

  return '0000'; 

} // get_working_db_version

/***********/
/*   PRIV  */
/***********/

// Return current DB version
function get_db_version() { 

  $sql = "SHOW TABLES LIKE 'app_info'"; 
  $db_results = \Dba::read($sql); 
  if (!\Dba::dbh()) {
      // DBH Failed uhh oohs
      exit; 
  }

  if (!\Dba::num_rows($db_results)) { 
    // They are pre app_info table or something is broken, fail
    exit; 
  }

  $sql = "SELECT * FROM `app_info` WHERE `key`='db_version'"; 
  $db_results = \Dba::read($sql); 
  $results = \Dba::fetch_assoc($db_results); 
  return $results['value']; 

} // get_version

// Checks https://gitorious.org/archie/archie/blobs/raw/master/docs/BUILD
function get_gitorious_version() { 


} // get_gitorious_version

?>
