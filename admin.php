<?php
require_once 'class/init.php'; 
if ($GLOBALS['user']->access < 100) { exit; } 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 

switch ($_REQUEST['action']) { 
	case 'export': 
		// wipe out any output so far we need to give them a file
		ob_end_clean(); 
                ob_implicit_flush(true);

                header("Content-Transfer-Encoding: binary");
                header("Cache-control: public");

		$date = date("dmY-hms",time()); 

		switch ($_REQUEST['type']) { 
			case 'csv': 
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: filename=\"archie-export-$date.csv\"");
				Record::export('csv'); 
				exit; 
			break; 
		} 
		
	break; 
	case 'confirm_delete': 
		//FIXME
		$record = new Record($_GET['record_id']); 
		echo "<fieldset class=\"record\"><legend>Confirmation</legend>";
		echo "Are you sure you want to delete " . scrub_out($record->site . '-' . $record->catalog_id) . "?";
		echo "<br /><input type=\"button\" value=\"Yes\" onclick=\"parent.location.href='" . Config::get('web_path') . "/admin.php?action=delete&record_id=" . $record->uid . "';\" />"; 
		echo "</fieldset>"; 
	break; 
	case 'delete': 
		Record::delete($_GET['record_id']); 
		echo "Record deleted..."; 
	break; 
	case 'manage': 
		require_once 'template/admin_manage.inc.php'; 	
	break; 
	case 'show_users': 
		$users = User::get_all();  
		require_once 'template/admin_users.inc.php'; 
	break; 
	case 'show_set_user_password':
		$client = new User($_GET['user_id']); 
		require_once 'template/set_user_password.inc.php'; 	
	break; 
	case 'set_user_password': 
		$client = new User($_POST['uid']); 
		if ($client->set_password($_POST['password'])) { 
			header("Location:" . Config::get('web_path') . '/admin.php?action=show_users'); 
			exit;
		} 
		require_once 'template/set_user_password.inc.php'; 
	break; 
	case 'disable_user': 
		$client = new User($_GET['uid']); 
		$client->disable(); 
		header("Location:" . Config::get('web_path') . '/admin.php?action=show_users'); 
		exit; 
	break; 
	case 'enable_user': 
		$client = new User($_GET['uid']); 
		$client->enable(); 
		header("Location:" . Config::get('web_path') . '/admin.php?action=show_users'); 
		exit; 
	break; 
	default: 

	break; 
} 

require_once 'template/footer.inc.php'; 

?>
