<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
define('NO_SESSION','1'); 
require_once 'class/init.php'; 

// For now it's only posts here
switch ($_POST['action']) { 
	case 'database':
	break;
  case 'admin':
  break;
	default: 
		// Nothin
	break; 
} // action
?>
