<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
define('NO_LOG',true); 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
require_once $prefix . '/class/init.php';
\UI\sess::set_user(User::get_from_username('cron'));
// Comment this out for it to actually work, can damage data so requires 
// manual intervention
print "This script can damage data, and is by default disabled, please open it and remove the exit(0); line if you want it to run\n";
exit(0);

$sql = "SELECT `uid`,`site` FROM `record`";
$db_results = Dba::read($sql);
$count=0;
while ($row = Dba::fetch_assoc($db_results)) { 
  \UI\sess::$user->site = new Site($row['site']);
  $ticket = new Content($row['uid'],'ticket');
  $record = new Record($row['uid']);
  if (isset($ticket->filename)) {
    $directory = dirname($ticket->filename); 
    if (!is_dir($directory)) { 
      mkdir($directory,0755,true); 
    }
    echo "Existing filename found, ensure $directory structure in place\n";
  }
  Content::write($row['uid'],'ticket',$ticket->filename);
  print "Writing Ticket for " . $record->catalog_id . " old filename " . $ticket->filename . "\n"; 
  flush();
  $count++;
}
print "Finished Rewrote $count tickets\n";
?>
