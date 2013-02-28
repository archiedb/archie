<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch ($GLOBALS['location']['action']) {
  case 'view':
    if ($GLOBALS['user']->uid != $GLOBALS['location']['objectid'] AND $GLOBALS['user']->access < 100) { 
      Event::error('DENIED','User ' . $GLOBALS['user']->username . ' attempted to view someone elses profile!'); 
      header('Location:' . Config::get('web_path')); 
      exit;
    }

    require_once Config::get('prefix') . '/template/user_view.inc.php';
  break;
  case 'passwordreset':

  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
