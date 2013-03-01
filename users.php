<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'view':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND \UI\sess::$user->access < 100) { 
      Event::error('DENIED','User ' . \UI\sess::$user->username . ' attempted to view someone elses profile!'); 
      header('Location:' . Config::get('web_path')); 
      exit;
    }
    require_once \UI\template(); 
  break;
  case 'passwordreset':

  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
