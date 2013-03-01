<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

// Pull the content from the URL 
$content = new Content(\UI\sess::location('objectid'),\UI\sess::location('action')); 
if (!$content->filename) { 
  // We have no file, nothing to display
  exit; 
}
// Send the headers and output the image, expires one day later (filenames should be unique)
header("Expires: " . gmdate("D, d M Y H:i:s",time()+86400));
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: $content->mime");
header("Content-Disposition: filename=" . scrub_out(basename($content->filename)));
echo $content->source(); 

?>
