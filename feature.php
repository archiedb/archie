<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    require_once \UI\template('/feature/new'); 
  break;
  case 'create':
  
  break;
  default: 
    // Rien a faire
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
