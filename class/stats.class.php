<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Stats { 
  
  // None of this!
  private function __construct() { }
  private function __clone() {}

  /**
   * total_records
   */
  public static function total_records($constraint='',$constraint_value='') {

    switch ($constraint) { 
      case 'today':
        $today = time()-86400; 
        $constraint_sql = ' AND `created`>=\'' . $today . '\'';
      break;
      case 'user':
        $uid = Dba::escape($constraint_value); 
        $constraint_sql = " AND `user`='$uid'"; 
      break;
      default:
        $constraint_sql = ''; 
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
        $constraint_sql = " AND `created`>='$today'";
      break;
      default:
        // Nothin
        $constraint_sql = ''; 
      break;
    } 

    $site = Dba::escape(Config::get('site')); 
    $sql = "SELECT COUNT(`uid`) AS `count`,`user` FROM `record` WHERE `site`='$site'" . $constraint_sql . " GROUP BY `user`";
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results); 

    // Nothing!@?
    if (!count($row)) { $row = array('count'=>'0'); }

    $user = new User($row['user']); 

    $row['user'] = $user->username;
    return $row; 

  } // worker_records

  /**
   * classification_records
   */
  public static function classification_records($constraint='',$constraint_value='') { 

    switch ($constraint) { 
        case 'today': 
          $today = time() - 86400; 
          $constraint_sql = " AND `created`>='$today'"; 
        break;
        case 'user':
          $uid = Dba::escape($constraint_value); 
          $constraint_sql = " AND `user`='$uid'"; 
        break; 
        default:
          // Nothin
          $constraint_sql = ''; 
        break;
    }

    $site = Dba::escape(Config::get('site'));  
    $sql = "SELECT COUNT(`uid`) AS `count`,`classification` FROM `record` WHERE `site`='$site'" . $constraint_sql . " GROUP BY `classification` ORDER BY `count` DESC"; 
    $db_results = Dba::read($sql); 
  
    $row = Dba::fetch_assoc($db_results); 
    
    // Nothing!?!@
    if (!count($row)) { return false; }
    $classification = new classification($row['classification']); 
    $row['classification'] = $classification->name ? $classification->name : 'UNDEF'; 

    return $row; 

  } // classification_records

} // Stats
