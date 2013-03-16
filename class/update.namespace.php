<?php namespace Update;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

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
      self::$git_version = file_get_contents('https://raw.github.com/vollmerk/archie/master/docs/BUILD');
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
          if ($update['version'] <= $current_version) {
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

    $sql = 'TRUNCATE `session`'; 
    $db_results = \Dba::write($sql); 

    $sql = 'TRUNCATE `temp_data`';
    $db_results = \Dba::write($sql); 

    $sql = 'OPTIMIZE `session`';
    $db_results = \Dba::write($sql); 

    $sql = 'OPTIMIZE `temp_data`';
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
    $update_string = '- Add notes to image.<br />' . 
                    '- Add user to image.<br />' . 
                    '- Remove data directory prefix from filename on media and image.<br />';
    $versions[] = array('version'=>'0002','description'=>$update_string); 
    $update_string = '- Add Northing,Easting,Elevation fields for station import.<br />' . 
                    '- Add indexes to commonly used fields.<br />' . 
                    '- Add notes and user to media table.<br />';
    $versions[] = array('version'=>'0003','description'=>$update_string); 
    $update_string = '- Reduce accuracy of xyz data.<br />'; 
    $versions[] = array('version'=>'0004','description'=>$update_string); 


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
    $methods = get_class_methods('\update\Database'); 

    self::$versions = self::define_versions();
    
    foreach (self::$versions as $update) { 
      // If it's newer then current then update
      if ($update['version'] > $current_version) { 
        $update_function = 'update_' . $update['version']; 
        if (in_array($update_function,$methods)) { 
          $success = call_user_func(array('\update\Database',$update_function));

          if ($success) { self::set_version($update['version']); }
          else { 
            \Event::error('DBUPGRADE','Database upgrade failed on version: ' . $update['version']);
            return false; 
          }
        }
      } // if it's newer

    } // end foreach

    // Run the post db upgrade operations
    self::post(); 

    return true; 

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

  /**
   * update_0002
   * - Add notes to image table
   * - Add user to image table
   */
  private static function update_0002() { 

    $retval = true; 

    $sql = "ALTER TABLE `image` ADD `notes` VARCHAR(512) NULL AFTER `type`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `image` ADD `user` INT(10) UNSIGNED NOT NULL AFTER `type`";
    $retval = \Dba::write($sql) ? $retval : false; 

    // Remove data_path from image and media tables
    $sql = "SELECT `uid`,`data` FROM `image`"; 
    $db_results = \Dba::read($sql); 

    while ($row = \Dba::fetch_assoc($db_results)) { 
      $row['data'] = ltrim($row['data'],\Config::get('data_root')); 
      $images[] = $row;       
    } 

    foreach ($images as $image) { 
      $data = \Dba::escape($image['data']); 
      $uid = \Dba::escape($image['uid']); 
      $sql = "UPDATE `image` SET `data`='$data' WHERE `uid`='$uid' LIMIT 1"; 
      \Dba::write($sql); 
    } // end foreach images

    $sql = "SELECT `uid`,`filename` FROM `media`"; 
    $db_results = \Dba::read($sql);

    while ($row = \Dba::fetch_assoc($db_results)) {
      $row['filename'] = ltrim($row['filename'],\Config::get('data_root')); 
      $media[] = $row; 
    }

    foreach ($media as $item) { 
      $filename = \Dba::escape($item['filename']); 
      $uid = \Dba::escape($item['uid']); 
      $sql = "UPDATE `media` SET `filename`='$filename' WHERE `uid`='$uid' LIMIT 1"; 
      \Dba::write($sql);  
    } // end foreach media

    return $retval; 

  } // update_0002

  /**
   * update_0003
   * - Add notes and user to media
   * - Add northing,easting,elevation fields to record
   * - Add index for user on media and image
   */
  private static function update_0003() { 
  
    $retval = true; 

    $sql = "ALTER TABLE `media` ADD `notes` VARCHAR(512) NULL AFTER `filename`"; 
    $retval = \Dba::write($sql) ? $retval : false;
    
    $sql = "ALTER TABLE `media` ADD `user` INT(10) UNSIGNED NOT NULL AFTER `filename`"; 
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE  `image` ADD INDEX (  `user` )"; 
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `media` ADD INDEX ( `user` )"; 
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` ADD UNIQUE ( `catalog_id` )";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` ADD `northing` DECIMAL( 8,6 ) NOT NULL AFTER `xrf_artifact_index`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` ADD `easting` DECIMAL( 8,6 ) NOT NULL AFTER  `northing`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` ADD `elevation` DECIMAL( 8,6 ) NOT NULL AFTER `easting`";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval; 

  } // update_0003

  /**
   * update_0004
   * Evidently they aren't that accurage, reduce decimal 
   */
  private static function update_0004() { 

    $retval = true; 

    $sql = "ALTER TABLE `record` CHANGE `northing` `northing` DECIMAL (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` CHANGE `easting` `easting` DECIMAL (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `record` CHANGE `elevation` `elevation` DECIMAL (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    return $retval; 

  } // update_0004

} // \Update\Database class

?>
