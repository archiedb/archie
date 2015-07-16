<?php namespace install;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * insert_db
 * Install the database using the connection
 * information provided
 */
function insert_db($info) { 

    // Make sure it's a valid DB name
    preg_match('/([^\d\w\_\-])/',$info['database'],$matches);

    if (count($matches)) { 
      Error::add('general','Invalid Database name.');
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
      \Error::add('general','Unable to create database - ' . $info['database'] . ' - ' .\Dba::error());
      return false;
    }

    $base_file = \Config::get('prefix') . '/config/database.sql'; 
    $data = file_get_contents($base_file);
    $pieces = split_sql($data);
    $errors = array();
    for ($i=0; $i<count($pieces); $i++) {
      $pieces[$i] = trim($pieces[$i]);
      if (substr($pieces[$i],0,2) != '--' AND substr($pieces[$i],0,2) != "/*" AND !empty($pieces[$i])) {
        if (!$db_results = \Dba::write($pieces[$i])) {
          $errors[] = array(\Dba::error(),$pieces[$i]);
        }
      }
    }

    if (count($errors)) {
      \Error::add('general',print_r($errors,1));
      $retval = false;
    }

    return $retval;

} // insert_db

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
