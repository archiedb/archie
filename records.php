<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
require_once 'template/header.inc.php'; 

// Switch on the action
switch (\UI\sess::location('action')) { 
	case 'upload_image': 
    Content::upload('record',$_POST['record_id'],$_POST,$_FILES); 
		$record = new Record($_POST['record_id']);
		require_once \UI\template('/edit_record'); 
	break; 
  case 'upload_media':
    Content::upload('media',$_POST['record_id'],$_POST,$_FILES); 
    $record = new Record($_POST['record_id']); 
    require_once \UI\template('/edit_record'); 
  break; 
  case 'image_edit': 
    if (!Access::has('image','write',$_POST['uid'])) { break; }
    Content::update('record',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
  case 'image_delete':
    if (!Access::has('image','delete',$_POST['uid'])) {  break; }
    $thumb = new Content($_POST['uid'],'thumb'); 
    if (!$thumb->delete()) { 
      Event::error('DELETE','Unable to delete thumbnail for record image:'. $_POST['uid']); 
    }
    $image = new Content($_POST['uid'],'record'); 
    if (!$image->delete()) { 
      Event::error('DELETE','Unable to delete record image:' . $_POST['uid']); 
    }

    // Return to whence we came,
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 

  break; 
	case 'update': 
		$record = new Record($_POST['record_id']); 
    // Set to current user  
    $_POST['user'] = \UI\sess::$user->uid;
		// Attempt to update this!
		if (!$record->update($_POST)) { 
			require_once 'template/edit_record.inc.php'; 
		} 
		else { 
      Event::add('success','Record has been updated, thanks!','small'); 
			$record = new Record($record->uid); 
			require_once \UI\template('/records/view'); 
	  } 
	break; 
  case 'edit':
		$record = new Record(\UI\sess::location('objectid')); 
		require_once 'template/edit_record.inc.php'; 
	break; 
  case 'view':
    $record = new Record(\UI\sess::location('objectid')); 
    require_once \UI\template();
  break;
  case 'new':
    require_once 'template/new_record.inc.php';
  break;
  case 'create':
    $_POST['user'] = \UI\sess::$user->uid;
    if ($record_id = Record::create($_POST)) {
      $record = new Record($record_id);
      require_once  \UI\template('/records/view');
    }
    else {
      require_once 'template/new_record.inc.php';
    }
  break;
  case 'delete': 
    // Admin only
    if (!Access::has('record','delete',$_POST['record_id'])) {  break; }
    // We should do some form ID checking here
    Record::delete($_POST['record_id']);
    header("Location:" . Config::get('web_path') . "/records"); 
    exit; 
  break;
  case 'print': 
    // For now its just tickets
    $ticket = new Content(\UI\sess::location('objectid'),'ticket'); 
    $record = new Record(\UI\sess::location('objectid')); 
    if (!$ticket->filename OR filemtime($ticket->filename) < $record->updated) { 
      Content::write(\UI\sess::location('objectid'),'ticket',$ticket->filename); 
    } 
    header("Location:" . Config::get('web_path') . '/media/ticket/' . \UI\sess::location('objectid'));
  break; 
  case 'search':
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_filter($_POST['field'],$_POST['value']); 
    $records = $view->run(); 
    require_once \UI\template('/show_records'); 
  break;
  case 'sort':
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'station_index';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_sort($field,$order); 
    $view->set_start(0); 
    $records = $view->run(); 
    require_once \UI\template('/show_records'); 
  break; 
  case 'offset': 
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_start(\UI\sess::location('objectid')); 
    $records= $view->run(); 
    require_once \UI\template('/show_records'); 
  break;
  default:
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_sort('station_index','ASC');
    $records = $view->run(); 
    require_once \UI\template('/show_records');
  break; 
} // end switch
?>
<?php require_once 'template/footer.inc.php'; ?>
