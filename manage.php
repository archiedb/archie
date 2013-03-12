<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
if (\UI\sess::$user->access < 100) { exit; } 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'regenerate':
    // Regenerate what!?
    switch (\UI\sess::location('objectid')) {
      case 'qrcode':
        Content::regenerate_qrcodes(); 
      break;
    }
    require_once 'template/manage_tools.inc.php'; 
  break;
  case 'tools':
    require_once \UI\template('/manage/tools'); 
  break; 
  default: 
  case 'status':
    // Include debug tools 
    require_once 'class/debug.namespace.php';
    require_once \UI\template('/manage/status'); 
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
