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
} 

require_once 'template/footer.inc.php'; 

?>
