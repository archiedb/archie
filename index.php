<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';

// The first is "what" we are doing
switch (\UI\sess::location('page')) { 
  case 'records':
    require_once 'records.php';
  break;
  case 'media':
    require_once 'image.php';
  break; 
  case 'manage': 
    require_once 'manage.php';
  break; 
  default:
  case 'stats':
    require_once 'stats.php';
  break; 
  case 'users':
    require_once 'users.php';
  break;
  case 'logout':
    vauth::logout(); 
  break; 
}
?>
