<?php namespace install;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * run
 * This does the install, and will revert all changes
 * if there's a failure
 */
function run($info) { 

  // Attepmt to install the Database
  $retval = insert_db($info);

  // Failure? 
  if (!$retval) {
    remove_db($info); 
    return false; 
  }

  $retval = write_config($info);

  if (!$retval) {
    delete_config();
    remove_db($info);
    return false;
  }

  $retval = initial_user($info); 

  if (!$retval) {
    delete_config();
    remove_db($info);
    return false; 
  }

  $retval = htaccess_enable();

  if (!$retval) {
    delete_htaccess();
    delete_config();
    remove_db($info);
    return false;
  }

  return true;

} // run

/**
 * delete_htaccess
 * removes the htaccess file
 */
function delete_htaccess() {
 
  $dir = dirname(__FILE__);
  $prefix = realpath($dir . "/../");
  unlink($prefix . '/.htaccess');

  return true; 

} //delete_htaccess
  
/**
 * delete_config
 * Removes the config!
 */
function delete_config() { 

  $dir = dirname(__FILE__);
  $prefix = realpath($dir . '/../');
  unlink($prefix . '/config/settings.php');

  return true; 

} // delete_config

/**
 * remove_db
 * Delete the database!
 */
function remove_db($info) {

  $sql = "DROP DATABASE `" . $info['database'] . "`";
  \Dba::write($sql); 

  return true; 

} // remove_db

/**
 * insert_db
 * Install the database using the connection
 * information provided
 */
function insert_db($info) { 

    // Make sure it's a valid DB name
    preg_match('/([^\d\w\_\-])/',$info['database'],$matches);

    if (count($matches)) { 
      Err::add('general','Invalid Database name.');
      return false; 
    }
    
    // Setup DB variables
    $retval = true; 

    // Set Config:: values
    \Config::set_by_array(array('database_name'=>$info['database'],
      'database_username'=>$info['username'],
      'database_password'=>$info['password'],
      'database_hostname'=>$info['hostname']));

    // Create the Database
    $sql = "CREATE DATABASE `" . $info['database'] . "`";
    $retval = \Dba::write($sql) ? $retval : false;

    \Dba::dbh($info['database'],true);

    if (!$retval) { 
      \Err::add('general','Unable to create database - ' . $info['database'] . ' - ' .\Dba::error());
      return false;
    }

    $base_file = \Config::get('prefix') . '/config/database.sql'; 
    $data = file_get_contents($base_file);
    $pieces = split_sql($data);
    $errors = array();
    for ($i=0; $i<count($pieces); $i++) {
      $pieces[$i] = trim($pieces[$i]);
      if (substr($pieces[$i],0,2) != '--' AND substr($pieces[$i],0,2) != "#" AND !empty($pieces[$i])) {
        if (!$db_results = \Dba::write($pieces[$i])) {
          $errors[] = array(\Dba::error(),$pieces[$i]);
        }
      }
    }

    if (count($errors)) {
      \Err::add('general',json_encode($errors));
      $retval = false;
    }

    return $retval;

} // insert_db

/**
 * write_config
 * Attempt to write out the Archie config
 */
function write_config($input) { 

  $retval = true; 

  // Attempt to write out the config
  $retval = \update\Code::config_update(array('database_username'=>$input['username'],
    'database_password'=>$input['password'],
    'database_hostname'=>$input['hostname'],
    'database_name'=>$input['database']));

  if (!$retval) { 
    \Err::add('general','Unable to write out config file, please check permissions');
  }

  return $retval; 

} // write_config


/**
 * initial_user
 * Create the first admin user
 */
function initial_user($input) { 

  // Attempt to insert the initial user, by now
  // we should have a working DBH() so just go for it
  $user_data = array('password'=>$input['admin_password'],
    'username'=>$input['admin_username'],
    'name'=>$input['admin_username'],
    'confirmpassword'=>$input['admin_pw_confirm'],
    'email'=>'admin@localhost',
    'site'=>'1');
  $retval = \User::create($user_data);

  if (!$retval) { 
    \Err::add('general','Unable to create initial Administrative account');
    return false;
  }

  \UI\sess::$user = new \User($retval);
  
  // We can be sure it's 1 because there is only 1.. to rule them all
  $retval = \UI\sess::$user->add_group('1');

  if (!$retval) { 
    \Err::add('general','Unable to add initial Admin account to Admin group');
    return false;
  }

  return $retval;

} // initial_user

/**
 * htaccess_enable
 * Move htaccess into place
 */
function htaccess_enable() {

  $data = file_get_contents(\Config::get('prefix') . '/htaccess.dist');
  $retval = file_put_contents(\Config::get('prefix') . '/.htaccess',$data);

  if (!$retval) { 
    \Err::add('general','Permission Denied installing .htaccess file');
  }

  return $retval;

} // htaccess_enable

/**
 * split_sql
 * splits up a standard SQL dump file into distinct sql queries
 */
function split_sql($sql) {
        $sql = trim($sql);
        $sql = preg_replace("/\n#[^\n]*\n/", "\n", $sql);
        $buffer = array();
        $ret = array();
        $in_string = false;
        for ($i=0; $i<strlen($sql)-1; $i++) {
                if ($sql[$i] == ";" && !$in_string) {
                        $ret[] = substr($sql, 0, $i);
                        $sql = substr($sql, $i + 1);
                        $i = 0;
                }
                if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                        $in_string = false;
                } elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                        $in_string = $sql[$i];
                }
                if (isset($buffer[1])) {
                        $buffer[0] = $buffer[1];
                }
                $buffer[1] = $sql[$i];
        }
        if (!empty($sql)) {
                $ret[] = $sql;
        }
        return($ret);
} // split_sql


?>
