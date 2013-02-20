<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
$GLOBALS['urlvar'] = explode("/",$_SERVER['REQUEST_URI']);
$www_prefix = explode("/",rtrim(Config::get('web_prefix'),'/')); 
foreach ($www_prefix as $prefix) { 
        array_shift($GLOBALS['urlvar']);
}

// The first is "what" we are doing
switch ($GLOBALS['urlvar']['0']) { 
        default:
        case 'records':
                require_once 'records.php';
        break;
        case 'user':
                require_once 'user.php';
        break;
}
?>
