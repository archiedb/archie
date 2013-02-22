<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
if ($GLOBALS['user']->access < 100) { exit; } 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch ($GLOBALS['location']['action']) {
  case 'regenerate':
    // Regenerate what!?
    switch ($GLOBALS['location']['objectid']) {
      case 'qrcode':
        Content::regenerate_qrcodes(); 
      break;
    }
    require_once 'template/manage_tools.inc.php'; 
  break;
  default: 
  case 'tools':
    // Include debug tools 
    require_once 'class/debug.namespace.php';
    require_once 'template/manage_tools.inc.php';
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
