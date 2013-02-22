<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Stats { 
  
  // None of this!
  private function __construct() { }
  private function __clone() {}

  /**
   * total_records
   */
  public static function total_records($constraint='') {

    switch ($constraint) { 
      case 'today':
        $constraint_sql = ' AND `entered`<=\'' . time()-86400 . '\'';
      break;
      default:
        // Nothin
      break;
    }

    $site = Dba::escape(Config::get('site'));
    $sql = "SELECT COUNT(`uid`) AS `total` FROM `record` WHERE `site`='$site'" . $constraint_sql; 
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results); 
    // Always return a number
    return intval($row['total']); 

  } // total_records

  /**
   * worker_records
   */
  public static function worker_records($constraint='') { 

    switch ($constraint) { 
      case 'today':
        // This isn't today
        $today = time()-86400;
        $constraint_sql = ' AND `entered`<=\'' . $today . '\'';
      break;
      default:
        // Nothin
      break;
    } 

    $site = Dba::escape(Config::get('site')); 
    $sql = "SELECT COUNT(`uid`) AS `total`,`user` FROM `record` GROUP BY `user` WHERE `site`='$site'" . $constraint_sql;
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results); 
    $user = new User($row['user']); 

    $row['user'] = $user->username;
    return $row; 

  } // worker_records

  /**
   * classification_records
   */
  public static function classification_records($constraint='') { 

    switch ($constraint) { 
        case 'today': 
          $today = time() - 86400; 
          $constraint_sql = ' AND `entered`<=\'' . $today . '\''; 
        break;
        default:
          // Nothin
        break;
    }

    $site = Dba::escape(Config::get('site'));  
    $sql = "SELECT COUNT(`uid`) AS `total`,`classification` FROM `record` GROUP BY `classification` WHETE `site`='$site'" . $constraint_sql; 
    $db_results = Dba::read($sql); 
  
    $row = Dba::fetch_assoc($db_results); 

    $classification = new classification($row['classification']); 
    $row['classification'] = $classification->name; 

    return $row; 

  } // classification_records

} // Stats
