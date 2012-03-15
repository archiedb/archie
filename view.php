<?php require_once 'class/init.php'; ?>
<?php require_once 'template/header.inc.php'; ?>
<?php 

switch ($_REQUEST['action']) { 
	default: 
		$records = Search::record('site',Config::get('site')); 
		require_once 'template/show_records.inc.php'; 
	break;
	case 'search': 
		if (!$_POST['field']) { $_POST['field'] = 'site'; } 
		if (!$_POST['value']) { $_POST['value'] = Config::get('site'); } 
		$records = Search::record($_POST['field'],$_POST['value']); 
		require_once 'template/show_records.inc.php'; 
	break; 
} // end switch
?>

<?php require_once 'template/footer.inc.php'; ?>

