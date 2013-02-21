<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
require_once '../class/init.php';

$sql = "SELECT `record`.`uid`,`media`.`filename` FROM `record` LEFT JOIN `media` ON `media`.`record`=`record`.`uid` AND `media`.`type`='qrcode'";
$db_results = Dba::read($sql); 

while ($row = Dba::fetch_assoc($db_results)) { 
  // Overwrite the existing file, if it exists!
  Content::write($row['uid'],'qrcode',$row['filename']); 
}
?>
