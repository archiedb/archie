<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
define('NO_SESSION','1'); 
define('NO_LOG','1');
define('INSTALL','1');
define('OUTDATED_DATABASE_OK',1);

// Full stop if config/settings.php is in place
if (file_exists('config/settings.php')) { echo "ALREADY INSTALLED"; exit; }

require_once 'class/debug.namespace.php';
require_once 'class/install.namespace.php';
require_once 'class/init.php'; 

// For now it's only posts here
switch ($_GET['action']) { 
	case 'database':
    require_once 'template/install/database.inc.php';
	break;
  case 'insertdb':
    // Attempt to install DB
    $retval = \Install\run($_POST);
    if (!$retval) { 
      require_once 'template/install/database.inc.php';
    }
    else {
      header("Location:login.php");
      exit;
    }

  break;
  case 'user':
    require_once 'template/install/user.inc.php';
  break;
	default: 
    require_once 'template/install/test.inc.php';
	break; 
} // action
?>
