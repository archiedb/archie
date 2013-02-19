<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 


require_once 'class/init.php'; 

$type = 'record';

if ($_GET['thumb']) { $type = 'thumb'; } 

$content = new Content($_GET['content_id'],$type); 


// Send the headers and output the image
header("Expires: Tue, 27 Mar 1984 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: $content->mime");
header("Content-Disposition: filename=" . scrub_out(basename($content->filename)));
echo $content->source(); 

?>
