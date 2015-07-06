<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
define('OUTDATED_DATABASE_OK','1'); 
define('NO_SESSION','1'); 
require_once 'class/init.php'; 

// For now it's only posts here
switch ($_POST['action']) { 
	case 'database':
    // Authenticate and make sure they are an administrator
		$username = scrub_in($_POST['username']); 
		$password = $_POST['password']; 

		$auth = vauth::authenticate($username,$password); 
		if ($auth['success']) {
			$user = User::get_from_username($username); 
      \UI\sess::$user = $user; 
      $is_admin = false; 
      // Check the old admin way
      if (isset($user->access)) { if ($user->access == 100) { $is_admin = true; } }
      if (!Access::is_admin() AND $is_admin === false) {
        Error::add('general','Invalid Username/Password or insufficient access level'); 
			}
			else { 
				$results = \update\Database::run(); 
				if (!$results) { exit(); }
				else { header('Location:' . Config::get('web_path')); exit(); }			
      }
		}
		Event::error('DENIED','Authentication Failure trying to update database'); 
    Error::add('general','Invalid Username/Password or insufficient access level'); 
		require_once Config::get('prefix') . '/template/database_upgrade.inc.php';
	break;
	default: 
		// Nothin
	break; 
} // action
?>
