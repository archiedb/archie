<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';

// The first is "what" we are doing
switch (\UI\sess::location('page')) { 
  case 'record':
  case 'records':
    if (!Access::has('record')) { \UI\access_denied(); }
    require_once 'records.php';
  break;
  case 'content':
    require_once 'content.php';
  break;
  case 'media':
    require_once 'image.php';
  break; 
  case 'viewer':
    require_once 'viewer.php';
  break;
  case 'manage': 
    require_once 'manage.php';
  break; 
  case 'level':
    if (!Access::has('level')) { \UI\access_denied(); }
    require_once 'level.php';
  break;
  case 'feature':
    if (!Access::has('feature')) { \UI\access_denied(); }
    require_once 'feature.php';
  break;
  case 'krotovina':
    if (!Access::has('krotovina')) { \UI\access_denied(); }
    require_once 'krotovina.php';
  break;
  default:
    require_once \UI\template('/header'); 
    require_once \UI\template('/menu');
    require_once \UI\template('/index');
    require_once \UI\template('/footer');
  break; 
  case 'reports':
    if (!Access::has('report')) { \UI\access_denied(); }
    require_once 'reports.php';
  break; 
  case 'users':
    if (!Access::has('user')) { \UI\access_denied(); }
    require_once 'users.php';
  break;
  case 'logout':
    vauth::logout(); 
  break; 
}
?>
