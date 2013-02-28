<?php
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
			print_r($user); 
			if ($user->access < '100') { 
				Event::error('DENIED','Access denied attempting to update database'); 
				exit(); 
			}
			else { 
				$results = \update\Database::run(); 
				if (!$results) { echo "NUTS Db update failed, you should restore your backup"; exit(); }
				else { header('Location:' . Config::get('web_path')); exit(); }			}
		}
		Event::error('DENIED','Authentication Failure trying to update database'); 
		require_once Config::get('prefix') . '/template/database_upgrade.inc.php';
	break;
	default: 
		// Nothin
	break; 
} // action
?>
