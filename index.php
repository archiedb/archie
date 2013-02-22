<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
$urlvar = explode("/",$_SERVER['REQUEST_URI']);
$www_prefix = explode("/",rtrim(Config::get('web_prefix'),'/')); 
foreach ($www_prefix as $prefix) { 
        array_shift($urlvar);
}
$GLOBALS['location'] = array(); 
$GLOBALS['location']['page'] = $urlvar['0']; 
$GLOBALS['location']['objectid'] = $urlvar['2'];
$GLOBALS['location']['action'] = $urlvar['1']; 

// The first is "what" we are doing
switch ($GLOBALS['location']['page']) { 
  case 'records':
    require_once 'records.php';
  break;
  case 'media':
    require_once 'image.php';
  break; 
  default:
  case 'stats':
    require_once 'stats.php';
  break; 
  case 'user':
    require_once 'user.php';
  break;
}
?>
