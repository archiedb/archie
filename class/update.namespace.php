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
      self::$git_version = file_get_contents('https://raw.github.com/archiedb/archie/master/docs/BUILD');
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

    $sql = 'OPTIMIZE TABLE `session`';
    $db_results = \Dba::write($sql); 

    $sql = 'OPTIMIZE TABLE `temp_data`';
    $db_results = \Dba::write($sql); 

    return true; 

   } // pre

   /**
    * post
    * Optimize tables as we've just made of mess of them
    */
  private static function post() { 

    $sql = 'SHOW TABLES';
    $db_results = \Dba::read($sql); 

    $tables = null;

    while ($table = \Dba::fetch_row($db_results)) {
      $tables .= "`" . $table['0']. "`,";
    }

    $tables = rtrim($tables,","); 

    $sql = "OPTIMIZE TABLE $tables";
    $db_results = \Dba::write($sql); 

    return true; 

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

  } // set_version 

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
    $update_string = '- Increase accuracy of weight,thickness and width to the thousandths.<br />';
    $versions[] = array('version'=>'0005','description'=>$update_string); 
    $update_string = '- Add `enabled` to material and classification.<br />' . 
                      '- Add level/krotovina/feature table.<br />';
    $versions[] = array('version'=>'0006','description'=>$update_string);
    $update_string = '- Add krotovina and feature and datum_location tables.<br />' . 
                      '- Update level table to conform to new method.<br />' .
                      '- Rename image.type to image.mime and add image.type as record type. <br />';
    $versions[] = array('version'=>'0007','description'=>$update_string); 
    $update_string = '- Add closed user and closed date to krotovina,level and feature.<br />';
    $versions[] = array('version'=>'0008','description'=>$update_string); 
    $update_string = '- Add primary image option to level, krotovina and feature.<br />' . 
                    '- Add site table.<br />' . 
                    '- Add record type to media table.<br />';
    $versions[] = array('version'=>'0009','description'=>$update_string); 
    $update_string = '- Add User->Site mapping.<br />' . 
                    '- Add User site preference.<br />' . 
                    '- Update Krotovina and Feature tables.<br />' . 
                    '- Add Indexes to speed up database queries.<br />';
    $versions[] = array('version'=>'0010','description'=>$update_string);
    $update_string = '- Rename datum_location table to spatial_data.<br />';
    $versions[] = array('version'=>'0011','description'=>$update_string);
    $update_string = '- Add krotovina to record table.<br />' . 
                    '- Rename level.record to level.catalog_id to match other tables.<br />';
    $versions[] = array('version'=>'0012','description'=>$update_string);
    $update_string = '- Add role based permissions.<br />' . 
                    '- Migrate Northing/Easting/Elevation to spatial_data table.<br />' . 
                    '- Drop unused fields from record table.<br />' .
                    '- Add per site user groups.<br />';
    $versions[] = array('version'=>'0013','description'=>$update_string);
    $update_string = "- Remove RN from record table.";
    $versions[] = array('version'=>'0014','description'=>$update_string);
    $update_string = '- Remove unused indexes from record table.<br />' . 
                    '- Set Station Index to Allowed NULL.';
    $versions[] = array('version'=>'0015','description'=>$update_string);
    $update_string = "- Add Spatial Data Index to fix performance issues with Record view.";
    $versions[] = array('version'=>'0016','description'=>$update_string);



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

  /**
   * update_0005
   * Adjust the accuracy on the height/weight/thickness
   */
  private static function update_0005() { 

    $retval = true; 

    $sql = "ALTER TABLE `record` CHANGE `weight` `weight` DECIMAL (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` CHANGE `thickness` `thickness` DECIMAL (8,3) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `record` CHANGE `height` `height` DECIMAL (8,3) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `record` CHANGE `width` `width` DECIMAL (8,3) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false; 

    return $retval; 

  } // update_0005

  /**
   * update_0006
   * Add enabled boolean to classification and materials
   */
  private static function update_0006() { 

    $retval = true; 
    $sql = "ALTER TABLE `material` ADD `enabled` INT (1) UNSIGNED DEFAULT '1' AFTER `name`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `classification` ADD `enabled` INT (1) UNSIGNED DEFAULT '1' AFTER `name`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `classification` ADD INDEX (`enabled`)"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `material` ADD INDEX (`enabled`)"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "CREATE TABLE `level` (" . 
          "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
          "`site` varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`record_id` varchar(255) NOT NULL," .
          "`unit` varchar(128) NOT NULL," . 
          "`quad`  varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`northing` decimal(8,3) NOT NULL," . 
          "`easting` decimal(8,3) NOT NULL," .
          "`type` varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`elv_a_start` decimal(8,3) NOT NULL," . 
          "`elv_a_finish` decimal(8,3) NOT NULL," . 
          "`elv_b_start` decimal(8,3) NOT NULL," . 
          "`elv_b_finish` decimal(8,3) NOT NULL," . 
          "`elv_c_start` decimal(8,3) NOT NULL," . 
          "`elv_c_finish` decimal(8,3) NOT NULL," . 
          "`elv_d_start` decimal(8,3) NOT NULL," . 
          "`elv_d_finish` decimal(8,3) NOT NULL," . 
          "`elv_center_start` decimal(8,3) NOT NULL," . 
          "`elv_center_finish` decimal(8,3) NOT NULL," . 
          "PRIMARY KEY (`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD INDEX (`site`)";
    $retval = \Dba::write($sql) ? $retval : false; 
 
    $sql = "ALTER TABLE `level` ADD INDEX (`record_id`)";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD INDEX (`type`)"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    return $retval; 

  } // update_0006

  /**
   * update_0007
   * Add krotovina table
   * Add feature table
   * Add datum_locations table
   * Rename level fields to match new values
   * Add excavators to level table
   * Rename record_id to level, and remove/re-add index
   * Add L.U. to level table
   * Add user, created,updated to level table
   * Remove type from level table and remove index
   */
  private static function update_0007() { 

    $retval = true; 

    $sql = "ALTER TABLE `image` ADD `mime` varchar(255) NOT NULL AFTER `record`";
    $retval = \Dba::write($sql) ? $retval : false; 

    // Itterate through all images and move the data around
    $sql = "SELECT * FROM `image`";
    $db_results = \Dba::read($sql);

    while ($row = \Dba::fetch_assoc($db_results)) { 
      $mime  = \Dba::escape($row['type']);
      $uid        = \Dba::escape($row['uid']); 
      $update_sql = "UPDATE `image` SET `mime`='$mime', `type`='record' WHERE `uid`='$uid'";
      $db_update = \Dba::write($update_sql); 
    }

    $sql = "ALTER TABLE `level` DROP `type`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `lsg_unit` int(10) UNSIGNED NOT NULL AFTER `quad`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `user` int(11) UNSIGNED NOT NULL AFTER `lsg_unit`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `created` int(10) UNSIGNED NOT NULL AFTER `user`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `updated` int(10) UNSIGNED NOT NULL AFTER `created`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `excavator_one` int(11) UNSIGNED AFTER `elv_center_finish`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `excavator_two` int(11) UNSIGNED AFTER `excavator_one`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `excavator_three` int(11) UNSIGNED AFTER `excavator_two`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `excavator_four` int(11) UNSIGNED AFTER `excavator_three`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `description` varchar(5000) AFTER `excavator_four`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `difference` varchar(5000) AFTER `description`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `notes` varchar(5000) AFTER `difference`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `closed` INT ( 1 ) UNSIGNED AFTER `notes`";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_a_start` `elv_nw_start` decimal (8,3) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_b_start` `elv_ne_start` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_c_start` `elv_sw_start` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_d_start` `elv_se_start` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_a_finish` `elv_nw_finish` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_b_finish` `elv_ne_finish` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_c_finish` `elv_sw_finish` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `elv_d_finish` `elv_se_finish` decimal (8,3) NOT NULL"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` CHANGE `record_id` `record` varchar(255) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "CREATE TABLE `datum_location` (" . 
          "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
          "`record` varchar(255) NOT NULL," . 
          "`record_type` varchar(255) NOT NULL," . 
          "`station_index` int(10) UNSIGNED NOT NULL," . 
          "`northing` decimal(8,3) NOT NULL," . 
          "`easting` decimal(8,3) NOT NULL," . 
          "`elevation` decimal(8,3) NOT NULL," . 
          "`note` varchar(255) NOT NULL," . 
          "PRIMARY KEY (`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "CREATE TABLE `krotovina` (" . 
          "`uid` int(11) NOT NULL AUTO_INCREMENT," .
          "`site` varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`record` varchar(255) NOT NULL," . 
          "`keywords` varchar(2048) NOT NULL," . 
          "`description` varchar(5000)," . 
          "`user` int(11) UNSIGNED NOT NULL," . 
          "`created` int(10) UNSIGNED NOT NULL, " . 
          "`updated` int(10) UNSIGNED NOT NULL, " .  
          "`closed` int(1) UNSIGNED NOT NULL," .
          "PRIMARY KEY (`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "CREATE TABLE `feature` (" . 
          "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
          "`site` varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`record` varchar(255) NOT NULL," . 
          "`keywords` varchar(2048) NOT NULL," . 
          "`description` varchar(5000)," . 
          "`user` int(11) UNSIGNED NOT NULL," . 
          "`created` int(10) UNSIGNED NOT NULL," . 
          "`updated` int(10) UNSIGNED NOT NULL," . 
          "`closed` int(1) UNSIGNED NOT NULL," .
          "PRIMARY KEY (`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"; 
    $retval = \Dba::write($sql) ? $retval : false; 
 
    return $retval; 

  } // update_0007

  /**
   * update_0008
   * Add closed time and user to level, krotovina and feature
   */
  private static function update_0008() {

    $retval = true;

    $sql = "ALTER TABLE `level` ADD `closed_date` int(10) UNSIGNED AFTER `closed`";
    $retval = \Dba::write($sql) ? true : false;

    $sql = "ALTER TABLE `level` ADD `closed_user` int(10) UNSIGNED AFTER `closed_date`";
    $retval = \Dba::write($sql) ? true : false;

    $sql = "ALTER TABLE `krotovina` ADD `closed_date` int(10) UNSIGNED AFTER `closed`";
    $retval = \Dba::write($sql) ? true : false;

    $sql = "ALTER TABLE `krotovina` ADD `closed_user` int(10) UNSIGNED AFTER `closed_date`";
    $retval = \Dba::write($sql) ? true : false;

    $sql = "ALTER TABLE `feature` ADD `closed_date` int(10) UNSIGNED AFTER `closed`";
    $retval = \Dba::write($sql) ? true : false;

    $sql = "ALTER TABLE `feature` ADD `closed_user` int(10) UNSIGNED AFTER `closed_date`";
    $retval = \Dba::write($sql) ? true : false;

    return $retval;

  } // update_0008

  /**
   * update_0009
   * - Add record_type to media table
   * - Add indexes where they make sense
   */
  private static function update_0009() { 

    $retval = true; 

    $sql = "ALTER TABLE `media` ADD `record_type` varchar(255) NOT NULL AFTER `record`"; 
    $retval = \Dba::write($sql) ? true : false; 

    $sql = "CREATE TABLE `site` (" . 
          "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
          "`name` varchar(255) CHARACTER SET utf8 NOT NULL," . 
          "`description` varchar(5000)," . 
          "`northing` varchar(1024)," .
          "`easting` varchar(1024)," . 
          "`elevation` varchar(1024)," .
          "`principal_investigator` int(11) UNSIGNED NOT NULL," .
          "`partners` varchar(5000)," . 
          "`excavation_start` int(11) UNSIGNED NOT NULL," . 
          "`excavation_end` int(11) UNSIGNED NOT NULL," . 
          "`enabled` int(1) UNSIGNED NOT NULL," .
          "PRIMARY KEY (`uid`)) " . 
          "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `level` ADD `image` int(11) UNSIGNED NOT NULL AFTER `notes`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` ADD `image` int(11) UNSIGNED NOT NULL AFTER `updated`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `feature` ADD `image` int(11) UNSIGNED NOT NULL AFTER `updated`"; 
    $retval = \Dba::write($sql) ? $retval : false; 

    return $retval; 

  } // update_0009

  /**
   * update_0010
   * - Drop feature.image field
   * - Drop feature.record field
   * - Alter feature.site make it an int(11)
   */
  public static function update_0010() { 

    $retval = true;

    $sql = "ALTER TABLE `feature` CHANGE `site` `site` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `feature` ADD INDEX (`site`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `feature` ADD INDEX (`record`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` ADD INDEX (`record`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `feature` DROP `image`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` DROP `image`";
    $retval = \Dba::write($sql) ? $retval: false;

    $sql = "ALTER TABLE `feature` CHANGE `record` `catalog_id` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false; 

    $sql = "ALTER TABLE `record` CHANGE `site` `site` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `record` ADD INDEX (`site`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` CHANGE `site` `site` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` CHANGE `record` `catalog_id` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `krotovina` ADD INDEX (`site`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `datum_location` CHANGE `record` `record` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `datum_location` ADD INDEX (`record_type`)";
    $retval = \Dba::write($sql) ? $retval : false;

    // Add field to users table for 'current site'
    $sql = "ALTER TABLE `users` ADD `site` INT(11) UNSIGNED NULL AFTER `password`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `site` CHANGE `principal_investigator` `principal_investigator` VARCHAR(255) NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false;

    // Add table for assigning site rights to users
    $sql = "CREATE TABLE `site_users` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
        "`site` int(11) UNSIGNED NOT NULL," . 
        "`user` int(11) UNSIGNED NOT NULL," . 
        "`level` int(11) UNSIGNED NOT NULL," . 
        "PRIMARY KEY (`uid`)) " .
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0010

  /**
   * update_0011
   * - Rename datum_location table
   */
  public static function update_0011() {

    $retval = true;

    $sql = "RENAME TABLE `datum_location` TO `spatial_data`";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0011

  /**
   * update_0012
   * Add krotovina field to record table
   * Rename level.record level.catalog_id for consistancy
   */
  public static function update_0012() {

    $retval = true;

    $sql = "ALTER TABLE `record` ADD `krotovina` INT(11) UNSIGNED NULL AFTER `feature`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `level` CHANGE `record` `catalog_id` INT(11) UNSIGNED NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0012

  /**
   * update_0013
   * - Migrate existing northing/easting/elevations to spatial_data
   * - Remove record.quad and record.unit, now linked directly to level
   * - Remove record.northing record.easting record.elevation, now stored in spatial_data table
   * - Switch record.level to NOT NULL
   * - Add tables for role based permissions
   * - Add tables for site permissions
   * - Migrate existing permissions
   * - Add additional 'info' fields to user table
   */
  public static function update_0013() { 

    $retval = true; 

    // Move record.[spatial_data] 
    $sql = "SELECT `northing`,`easting`,`elevation`,`station_index`,`uid` AS `record` FROM `record`";
    $db_results = \Dba::read($sql); 

    while ($row = \Dba::fetch_assoc($db_results)) { 

      // If it's empty we don't give a shit
      if ($row['northing'] == '0.000' AND $row['easting'] == '0.000' AND $row['elevation'] == '0.000') {
        continue; 
      }

      $sql = "INSERT INTO `spatial_data` (`record`,`record_type`,`station_index`,`northing`,`easting`,`elevation`) " . 
          "VALUES ('" . $row['record'] . "','record','" . $row['station_index'] . "','" . $row['northing'] . "','" . $row['easting'] . "','" . $row['elevation'] . "')";
      $retval = \Dba::write($sql) ? $retval : false;

    } 


    // Switch record.level to NOT NULL
    $sql = "ALTER TABLE `record` CHANGE `level` `level` INT(11) UNSIGNED NOT NULL";
    $retval = \Dba::write($sql) ? $retval : false;

    // We've moved everything drop the extranous information
    $sql = "ALTER TABLE `record` DROP `quad`";
    $retval = \Dba::write($sql) ? $retval : false;
    $sql = "ALTER TABLE `record` DROP `unit`";
    $retval = \Dba::write($sql) ? $retval : false;
    $sql = "ALTER TABLE `record` DROP `northing`";
    $retval = \Dba::write($sql) ? $retval : false;
    $sql = "ALTER TABLE `record` DROP `easting`";
    $retval = \Dba::write($sql) ? $retval : false;
    $sql = "ALTER TABLE `record` DROP `elevation`";
    $retval = \Dba::write($sql) ? $retval : false;

    // Add role table
    $sql = "CREATE TABLE `role` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
        "`name` varchar(255) NOT NULL," . 
        "`description` varchar(512) NOT NULL," . 
        "PRIMARY KEY (`uid`)) " .
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    // Insert the different potential roles
    $roles = array('user'=>'Users',
              'record'=>'Records',
              'feature'=>'Features',
              'krotovina'=>'Krotovina',
              'media'=>'Media',
              'site'=>'Site',
              'level'=>'Level',
              'report'=>'Reports',
              'admin'=>'Admin Functions');
    // Itterate through and add them in
    foreach ($roles as $name=>$desc) { 
      $sql = "INSERT INTO `role` (`name`,`description`) VALUES ('$name','$desc')";
      $retval = \Dba::write($sql) ? $retval : false;
    }

    $sql = "CREATE TABLE `action` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," . 
        "`name` varchar(255) NOT NULL," . 
        "`description` varchar(512) NOT NULL," .
        "PRIMARY KEY (`uid`)) " . 
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;


    $actions = array('read'=>'Read',
                'create'=>'Create',
                'edit'=>'Edit',
                'close'=>'Close',
                'delete'=>'Delete',
                'reopen'=>'Re-open',
                'manage'=>'Manage',
                'admin'=>'Admin');
    // Itterate through and add the current ones 
    foreach ($actions as $name=>$desc) {
      $sql = "INSERT INTO `action` (`name`,`description`) VALUES ('$name','$desc')";
      $retval = \Dba::write($sql) ? $retval : false;
    }

    $sql = "CREATE TABLE `role_action` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," .
        "`role` int(11) NOT NULL," .
        "`action` int(11) NOT NULL," .
        "PRIMARY KEY (`uid`)) " .
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    // Now for the mappings Role -> Action
    $role_action = array('user'=>array('read','create','edit','manage','admin'),
        'record'=>array('read','create','edit','manage','admin'),
        'feature'=>array('read','create','edit','manage','admin'),
        'krotovina'=>array('read','create','edit','manage','admin'),
        'level'=>array('read','create','edit','reopen','manage','admin'),
        'report'=>array('read','create','admin'),
        'media'=>array('read','create','delete','admin'),
        'admin'=>array('manage','admin'),
        'site'=>array('read','create','edit','manage','admin'));

    // need to look up some UIDs for this shit
    $sql = "SELECT `uid`,`name` FROM `role`";
    $db_results = \Dba::read($sql);
    while ($results = \Dba::fetch_assoc($db_results)) {
      $roles[$results['name']] = $results['uid'];
    }
    $sql = "SELECT `uid`,`name` FROM `action`";
    $db_results = \Dba::read($sql);
    while ($results = \Dba::fetch_assoc($db_results)) {
      $actions[$results['name']] = $results['uid'];
    }

    foreach ($role_action as $role=>$actionlist) { 
      foreach ($actionlist as $action) {
       $sql = "INSERT INTO `role_action` (`role`,`action`) VALUES (?,?)";
       $retval = \Dba::write($sql,array($roles[$role],$actions[$action])) ? $retval : false;
      } 
    } 

    $sql = "CREATE TABLE `group` (" .
        "`uid` int(11) NOT NULL AUTO_INCREMENT," .
        "`name` varchar(255) NOT NULL," . 
        "`description` varchar(512) NOT NULL," .
        "PRIMARY KEY (`uid`)) " .
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    // Create initial administrators group
    $sql = "INSERT INTO `group` (`name`,`description`) VALUES ('Full Admin','Application Administrators')";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "CREATE TABLE `group_role` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," .
        "`group` int(11) NOT NULL," .
        "`role` int(11) NOT NULL," . 
        "`action` int(11) NOT NULL," .
        "PRIMARY KEY (`uid`)) " . 
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "CREATE TABLE `user_group` (" . 
        "`uid` int(11) NOT NULL AUTO_INCREMENT," .
        "`user` int(11) NOT NULL," .
        "`group` int(11) NOT NULL," .
        "`site` int(11) NOT NULL," .
        "PRIMARY KEY (`uid`)) " . 
        "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "SELECT `uid` FROM `site` ORDER BY `uid` ASC LIMIT 1";
    $db_results = \Dba::read($sql);
    $row = \Dba::fetch_assoc($db_results);
    $site = $row['uid'];

    // We should add the existing "100" level users to the Full Admin group
    // for the existing site
    $sql = "SELECT `uid` FROM `users` WHERE `access`='100' AND disabled IS NULL";
    $db_results = \Dba::read($sql); 

    while ($row = \Dba::fetch_assoc($db_results)) { 

      $sql = "INSERT INTO `user_group` (`user`,`group`,`site`) VALUES ('" . $row['uid'] . "','1','$site')";
      $retval = \Dba::write($sql) ? $retval : false;

    } 

    $sql = "SELECT `uid` FROM `role` WHERE `name`='admin'";
    $db_results = \Dba::read($sql);
    $row = \Dba::fetch_assoc($db_results);
    $role_admin = $row['uid'];
  
    $sql = "SELECT `uid` FROM `action` WHERE `name`='admin'";
    $db_results = \Dba::read($sql);
    $row = \Dba::fetch_assoc($db_results);
    $action_admin = $row['uid'];

    // Add the 'admin/admin' role to the admin group
    $sql = "INSERT INTO `group_role` (`group`,`role`,`action`) VALUES ('1','$role_admin','$action_admin')";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `users` DROP `access`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "CREATE VIEW `user_permission_view` AS SELECT DISTINCT `user_group`.`site` AS `site`,`user_group`.`user` AS `user`,`role`.`name` AS `role`,`action`.`name` AS `action` " .
      "FROM `group`,`role`,`action`,`user_group` JOIN `group_role` ON `user_group`.`group`=`group_role`.`group` " .
      "WHERE `group_role`.`role`=`role`.`uid` AND `group_role`.`action`=`action`.`uid`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `users` ADD `last_login` INT(11) UNSIGNED NULL AFTER `site`";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "ALTER TABLE `users` ADD `notes` INT(11) UNSIGNED NULL AFTER `site`";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0013

  /**
   * update_0014
   * - Remove station_index from record table
   * - Migration RN only info to spatial_data
   */
  public static function update_0014() { 

    $retval = true; 

    $sql = "SELECT `uid`,`station_index` FROM `record` WHERE `station_index` IS NOT NULL";
    $db_results = \Dba::read($sql);

    while ($row = \Dba::fetch_assoc($db_results)) { 
    
      // Make sure there's not already a spatial_data record
      $sql = "SELECT * FROM `spatial_data` WHERE `record_type`='record' AND `record`=?";
      $exists = \Dba::read($sql,array($row['uid']));

      if ($point = \Dba::fetch_assoc($db_results)) {
        $sql = "UPDATE `spatial_data` SET `station_index`=? WHERE `uid`=?";
        $retval = \Dba::write($sql,array($row['station_index'],$point['uid']));
      }
      else {
        $sql = "INSERT INTO `spatial_data` (`record`,`record_type`,`station_index`) VALUES (?,?,?)";
        $retval = \Dba::write($sql,array($row['uid'],'record',$row['station_index'])) ? $retval : false;
      }

    } // end while station_index

    $sql = "ALTER TABLE `record` DROP `station_index`"; 
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0014

  /**
   * update_0015
   * - Adjust spatial_data allow station_index to be NULL
   */
  public static function update_0015() { 

    $retval = true; 

    $sql = "ALTER TABLE `spatial_data` CHANGE `station_index` `station_index` INT(11) UNSIGNED";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "UPDATE `spatial_data` SET `station_index` = NULL WHERE `station_index`='0'";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0015

  /**
   * update_0016
   * - Add spaital_data.record Index to fix performance issues with single record view
   * - Fix potential duplicate spatial_data records
   */
  public static function update_0016() { 

    $retval = true; 

    $sql = "ALTER TABLE `spatial_data` ADD INDEX(`record`)";
    $retval = \Dba::write($sql) ? $retval : false;

    $sql = "SELECT * FROM `spatial_data` WHERE `record_type`='record'";
    $db_results = \Dba::read($sql);

    $found = array();

    // Itterate over all of them and fix duplicates
    while ($row = \Dba::fetch_assoc($db_results)) { 

      if (isset($found[$row['record_type']][$row['record']])) { 
        $prev = $found[$row['record_type']][$row['record']];
        // Try to figure out which one should be updated, and which should be deleted
        if ($row['northing'] == 0 AND $row['easting'] == 0 AND $row['elevation'] == 0 AND $row['station_index']) {
          $sql = "UPDATE `spatial_data` SET `station_index`=? WHERE `uid`=?";
          $retval = \Dba::write($sql,array($row['station_index'],$prev['uid'])) ? $retval : false;
          $sql = "DELETE FROM `spatial_data` WHERE `uid`=?";
          $retval = \Dba::write($sql,array($row['uid'])) ? $retval : false;
        } // if this is the junk
        if ($prev['northing'] == 0 AND $prev['easting'] == 0 AND $prev['elevation'] == 0 AND $prev['station_index']) {
          $sql = "UPDATE `spatial_data` SET `station_index`=? WHERE `uid`=?";
          $retval = \Dba::write($sql,array($prev['station_index'],$row['uid'])) ? $retval : false;
          $sql = "DELETE FROM `spatial_data` WHERE `uid`=?";
          $retval = \Dba::write($sql,array($prev['uid'])) ? $retval : false;
          $found[$row['record_type']][$row['record']] = $row;
        } // if prev is the junk one
      }
      else { 
        $found[$row['record_type']][$row['record']] = $row;
      }

    } // end while spatial_data

    return $retval;

  } // update_0016

 /**
   * update_0017
   * - Drop site_users table, it's not needed!
   * - Add "Project" to site
   */
  public static function update_0017() { 

    $retval = true; 

    $sql = "DROP TABLE `site_users`";
    $db_results = \Dba::write($sql);
  
    $sql = "ALTER TABLE `site` ADD `project` varchar(128) AFTER `project`";
    $retval = \Dba::write($sql) ? $retval : false;

    return $retval;

  } // update_0017



} // \Update\Database class

?>
