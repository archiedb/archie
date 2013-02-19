<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
$GLOBALS['urlvar'] = explode("/",$_SERVER['REQUEST_URI']);
$www_prefix = explode("/",Config::get('web_prefix')); 
foreach ($www_prefix as $prefix) { 
        array_shift($GLOBALS['urlvar']);
}
//If the user requested '/page/6/about-elephans/' then $urlVariables[1] would be 'page', $urlVariables[2] would be '6' and so on.

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
