<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
define('NO_LOG',true); 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
require_once $prefix . '/class/init.php';

// We need a user here so we know which site we're working on, FIXME
// Use a reserved user name? 
\UI\sess::set_user(User::get_from_username('cron')); 

// For now we're just doing the levels, and we're not going to
// worry about anything else 

// Get all levels
$sql = "SELECT `uid` FROM `level`";
$db_results = Dba::read($sql); 

while ($row = Dba::fetch_assoc($db_results)) { 
  $level = new Level($row['uid']);

  // Check and see if we could close it
  // Assume boxes are check'd and shit
  $input = array('kroto_sample'=>1,'kroto_bag'=>1,'level_photo'=>1,'notes_done'=>1,'connect'=>1);
  $return = $level->validate_close($input); 
  if (!$return) {
    echo $level->name . " is a junk level - reopening\n";
//    $level->open();
  }
} // end levels

$sql = "SELECT `uid` FROM `record`";
$db_results = Dba::read($sql);

while ($row = Dba::fetch_assoc($db_results)) {
  $record = new Record($row['uid']);
  if (is_object($record->level)) { 
    echo "Catalog #" . $record->catalog_id . " [" . $record->site->name . "] has no Level\n";
  }
}
?>
