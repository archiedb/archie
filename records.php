<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
require_once 'template/header.inc.php'; 
if ($GLOBALS['location']['objectid']) { 
  $_GET['record_id'] = $GLOBALS['location']['objectid'];
}
// Switch on the action
switch ($GLOBALS['location']['action']) { 
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
	case 'update': 
		$record = new Record($_POST['record_id']); 
    // Set to current user  
    $_POST['user'] = $GLOBALS['user']->uid;
		// Attempt to update this!
		if (!$record->update($_POST)) { 
			require_once 'template/edit_record.inc.php'; 
		} 
		else { 
			$record = new Record($record->uid); 
			require_once 'template/show_record.inc.php';
	} 
	break; 
  case 'edit':
		$record = new Record($_GET['record_id']); 
		require_once 'template/edit_record.inc.php'; 
	break; 
  case 'search':
    if (!$_POST['field']) { $_POST['field'] = 'site'; }
    if (!$_POST['value']) { $_POST['value'] = Config::get('site'); }
    $records = Search::record($_POST['field'],$_POST['value']);
    require_once 'template/show_records.inc.php';
  break;
  case 'view':
    $record = new Record($_GET['record_id']); 
    require_once 'template/show_record.inc.php';
  break;
  case 'new':
    require_once 'template/new_record.inc.php';
  break;
  case 'create':
    $_POST['user'] = $GLOBALS['user']->uid;
    if ($record_id = Record::create($_POST)) {
      $record = new Record($record_id);
      require_once 'template/show_record.inc.php';
    }
    else {
      require_once 'template/new_record.inc.php';
    }
  break;
  case 'delete': 
    // Admin only
    if ($GLOBALS['user']->access < '100') { break; }
    // We should do some form ID checking here
    Record::delete($_POST['record_id']);
    header("Location:" . Config::get('web_path') . "/records"); 
    exit; 
  break;
  case 'print': 
    // For now its just tickets
    $ticket = new Content($_GET['record_id'],'ticket'); 
    $record = new Record($_GET['record_id']); 
    if (!$ticket->filename OR filemtime($ticket->filename) < $record->updated) { 
      Content::write($_GET['record_id'],'ticket',$ticket->filename); 
    } 
    header("Location:" . Config::get('web_path') . '/media/ticket/' . $_GET['record_id']);
  break; 
  case 'sort':
    $order = isset($GLOBALS['location']['objectid']) ? $GLOBALS['location']['objectid'] : 'station_index';
    $records = Search::record('site',Config::get('site'),$order); 
    require_once 'template/show_records.inc.php';
  break;
  default:
    $records = Search::record('site',Config::get('site'));
    require_once 'template/show_records.inc.php';
  break; 
} // end switch
?>
<?php require_once 'template/footer.inc.php'; ?>
