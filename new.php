<?php require_once 'class/init.php'; ?>
<?php require_once 'template/header.inc.php'; ?>
<?php 

switch ($_REQUEST['action']) { 
	case 'upload_image': 

		$path_info = pathinfo($_FILES['image']['name']); 
		$upload['file'] = $_FILES['image']['tmp_name']; 
		$upload['mime'] = 'image/' . $path_info['extension']; 

		// Allowed image types 
		$allowed_types = array('png','jpg','tiff','gif'); 
		if (!in_array(strtolower($path_info['extension']),$allowed_types)) { 
			Error::add('upload','Invalid File Type, PNG,JPG,TIFF & GIF Allowed'); 
		} 
		
		// Make sure we got something
		if (empty($_FILES['image']['tmp_name'])) { 
			Error::add('upload','No Image uploaded'); 
		} 

		if (Error::occurred()) { 
			$record = new Record($_POST['record_id']); 
			require_once 'template/edit_record.inc.php'; 
			break; 
		} 
		$handle = fopen($upload['file'],'rb');
		$image_data = fread($handle,filesize($upload['file']));
		fclose($handle); 
		
		// If thumbnail generation worked, lets write it!
		if (!$thumb = Image::generate_thumb($image_data,array('height'=>150,'width'=>150),$path_info['extension'])) { 
			Event::error('Image','Thumb from Upload not generated'); 
		} 
		else { 
			Content::write($_POST['record_id'],'thumb',$thumb,$upload['mime']); 
		} 

		if (!Content::write($_POST['record_id'],'record',$image_data,$upload['mime'])) { 
			Error::add('upload','Upload failed'); 
		}
		$record = new Record($_POST['record_id']);
		require_once 'template/edit_record.inc.php';
	
	break; 
	case 'edit': 
		$record = new Record($_GET['record_id']); 
		require_once 'template/edit_record.inc.php'; 
	break; 
	case 'update': 
		$record = new Record($_POST['record_id']); 
		// Attempt to update this!
		if (!$record->update($_POST)) { 
			require_once 'template/edit_record.inc.php'; 
		} 
		else { 
			$record = new Record($_POST['record_id']); 
			require_once 'template/show_record.inc.php';
	} 
	break; 
	break; 
	// Create that new record
	case 'create': 
		// We are the current user
		$_POST['user'] = $GLOBALS['user']->uid;  
		if ($record_id = Record::create($_POST)) { 
			$record = new Record($record_id); 
			require_once 'template/show_record.inc.php'; 
		} 
		else { 
			require_once 'template/new_record.inc.php'; 
		} 
	break;
	// Default to creating a new record
	default: 
		require_once 'template/new_record.inc.php'; 
	break;
} // end switch
?>

<?php require_once 'template/footer.inc.php'; ?>

