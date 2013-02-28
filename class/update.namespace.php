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
  $this_version = intval(Code::version()); 

  if ($this_version < $git_version) { return false; }

  return true; 

}

/**
 * check_db_version
 * Checks to see if the db is up to date
 */
function check_db_version() { 

  $working_version = intval(Database::version()); 
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

// Checks https://gitorious.org/archie/archie/blobs/raw/master/docs/BUILD
function get_gitorious_version() { 


} // get_gitorious_version

/**
 * Code
 * Class to check for updates needed to the codebase
 */
class Code {

  private static $code_version;
  private static $git_version;
  private static $stable_version; // Maybe we will use this one day

  // None of this
  private function __construct() {} 
  private function __clone() {}

  /**
   * return version of the code
   */
  public static function version() { 

    if (!self::$code_version) { 
      self::$code_version = file_get_contents(\Config::get('prefix') . '/docs/BUILD');
    }

    return self::$code_version; 

  } // version

  /**
   * git_version
   * Return the version as we see in the git repo
   */
  public static function git_version() { 
    
    // Avoid asking more then once per page load
    if (!self::$git_version) { 
      self::$git_version = file_get_contents('https://gitorious.org/archie/archie/blobs/raw/master/docs/BUILD');
    } 
    return self::$git_version;

  } //git_version

  /**
   * check
   * Return T/F if code is up to date
   */
  public static function check() { 

    if (self::version() < self::git_version()) { return false; }
    
    return true; 

  } // check

} // \Update\code

/**
 * Database
 * class for updating the database
 */
class Database {

  private static $db_version;
  private static $versions = array(); 

  // None of this
  private function __construct() { }
  private function __clone() { }

  /**
   * return version of the db
   */
  public static function version() { 

    $sql = "SHOW TABLES LIKE 'app_info'"; 
    $db_results = \Dba::read($sql); 
    if (!\Dba::dbh()) {
        // DBH Failed uhh oohs
        \Event::error('Database','Unable to connect to database');
        exit; 
    }

    if (!\Dba::num_rows($db_results)) { 
      // They are pre app_info table or something is broken 
      \Event::error('Database','Error code version too new, and db isnt updated!');
      return '0000';
// COMMENTED OUT 
//      exit;
    }

    $sql = "SELECT * FROM `app_info` WHERE `key`='db_version'"; 
    $db_results = \Dba::read($sql); 
    $results = \Dba::fetch_assoc($db_results); 
    return $results['value']; 

  } // version

  /**
   * check
   * Return false if we need to update the database
   */
  public static function check() { 

    self::$versions = self::define_versions(); 
    $current_version = self::version(); 

    foreach (self::$versions as $update) { 
      if ($update['version'] > $current_version) {
        return false; 
      }
    }

    return true; 

  } // check

  /**
   * get_versions
   * Return all of the possible versions, with possible filters
   */
  public static function get_versions($filter='') { 

    self::$versions = self::define_versions(); 

    switch ($filter) { 
      case 'new': 
        $current_version = self::version();
        foreach (self::$versions as $key=>$update) { 
          if ($update['version'] < $current_version) {
            unset(self::$versions[$key]); 
          } 
        }
        return self::$versions;
      break;
      default:
        return self::$versions;
      break;
    }

  } // get_versions

  /**
   * pre
   * Prep the database to be updated - aka remove existing sessions
   */
  private static function pre() { 

    $sql = "TRUNCATE `session`"; 
    $db_results = \Dba::write($sql); 

    return true; 

   } // pre

   /**
    * post
    * Optimize tables as we've just made of mess of them
    */
  private static function post() { 



  } // post

  /**
   * set_version
   * Sets the new database version
   */
  private static function set_version($value) { 

    $value = \Dba::escape($value); 
    $sql = "UPDATE `app_info` SET `value`='$value' WHERE `key`='db_version'"; 
    $db_results = \Dba::write($sql); 

    return true; 

  } 

  /**
   * define_versions
   * This is a list of the db updates that exist, maybe a crappy way of doing it?
   */
  private static function define_versions() { 

    $versions = array(); 

    $update_string = '- Add app_info table for tracking DB version internally.<br />' . 
                    '- Add temp_data table for browse/filter/sorting.<br />'; 
    $versions[] = array('version'=>'0001','description'=>$update_string); 


    return $versions; 


  } // define_versions

  /**
   * run
   * Runs the database upgrade, hope they backed up!
   */ 
  public static function run() { 

    // Run the pre-upgrade operations
    self::pre(); 

    // Don't want to run out of time
    set_time_limit(0); 

    $current_version = self::version(); 

    $methods = get_class_methods('Database'); 

    if (!is_array((self::$versions))) { 
      self::$versions = self::define_versions();
    } 

    foreach (self::$versions as $version) { 

      // If it's newer then current then update
      if ($versions['version'] > $current_version) { 
        $update_function = 'update_' . $version['version']; 
        if (in_array($update_function,$methods)) { 
          $success = call_user_func(array('Database',$update_function));

          if ($success) { self::set_version('db_version',$version['version']); }
          else { 
            \Event::error('DBUPGRADE','Database upgrade failed on version: ' . $version['version']);
            return false; 
          }
        }
      }

    } // end foreach

    // Run the post db upgrade operations
    self::post(); 

  } // run

  /********* UPDATE FUNCTIONS **********/

  /**
   * update_0001
   */
  private static function update_0001() { 

    $retval = true; 

    $sql = "CREATE TABLE `app_info` ( `key` varchar(128) CHARACTER SET utf8 DEFAULT NULL, `value` varchar(255) CHARACTER SET utf8 DEFAULT NULL, UNIQUE KEY `key` (`key`)) " . 
          " ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "INSERT INTO `app_info` (`key`,`value`) VALUES ('db_version','0001')"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "CREATE TABLE `temp_data` (" . 
          "`uid` int(13) NOT NULL AUTO_INCREMENT," . 
          "`sid` varchar(128) CHARACTER SET utf8 NOT NULL," . 
          "`data` longtext COLLATE utf8_unicode_ci NOT NULL," . 
          "`objects` longtext COLLATE utf8_unicode_ci NOT NULL," . 
          "PRIMARY KEY (`sid`,`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    return $retval; 

  } // update_0001

} // \Update\Database class

?>
