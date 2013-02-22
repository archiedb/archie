<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
require_once 'template/header.inc.php'; 

// Switch on the action
switch ($GLOBALS['location']['action']) { 
  default:
    require_once 'template/index.inc.php';
  break;
}
?>
