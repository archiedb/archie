<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
define('NO_LOG',true); 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
require_once $prefix . '/class/init.php';

// Look for report requests, array of filenames!
$requests = Report::current_requests(); 

foreach ($requests as $request) { 
  // Pull in the data
  $content = file_get_contents($request); 
  // Ok we get it, you want a request, delete it so nobody else tries
  unlink($request); 
  
  $file_info = explode("_",basename($request,'.request')); 

  $options = explode("\n",$content); 

  $report = new Report($file_info['0'],$file_info['1']); 
  $report->generate($options['0']); 
  // Send an e-mail?
  if (strlen($options['1'])) { 
    mail($options['1'],'Archie Report Finished','Your report is done!');
  }
} 

?>
