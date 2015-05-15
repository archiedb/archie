<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
require_once 'template/header.inc.php'; 

// Switch on the action
switch (\UI\sess::location('action')) { 
  case 'upload':
    if (!Access::has('media','create')) { \UI\access_denied(); }
    Content::upload($_POST['record_id'],$_POST,$_FILES,'record'); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_edit': 
    if (!Access::has('media','edit')) { \UI\access_denied(); }
    Content::update('image',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
  case '3dmodel_edit':
    if (!Access::has('media','edit')) { \UI\access_denied(); }
    Content::update('3dmodel',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_delete':
    if (!Access::has('media','delete')) { \UI\access_denied(); }
    $image = new Content($_POST['uid'],'image'); 
    if (!$image->delete()) { 
      Error::add('delete','Unable to perform image deletion request, please contact administrator'); 
    }
    else { 
      Event::add('success','Image Deleted','small'); 
    }
    // Return to whence we came,
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
  case '3dmodel_delete':
    if (!Access::has('media','delete')) { \UI\access_denied(); }
    $media = new Content($_POST['uid'],'3dmodel'); 
    if (!$media->delete()) { 
      Event::error('DELETE','Unable to delete media item:' . $media->filename); 
      Error::add('delete','Unable to 3D Model perform deletion request, please contact administrator'); 
    }
    
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'media_delete':
    if (!Access::has('media','delete')) { \UI\access_denied(); }
    $media = new Content($_POST['uid'],'media'); 
    if (!$media->delete()) { 
      Event::error('DELETE','Unable to delete media item:' . $media->filename); 
      Error::add('delete','Unable to Media perform deletion request, please contact administrator'); 
    }
    
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
	case 'update': 
    if (!Access::has('record','edit')) { \UI\access_denied(); }
		$record = new Record($_POST['record_id']); 
    // Set to current user  
    $_POST['user'] = \UI\sess::$user->uid;
		// Attempt to update this!
		if (!$record->update($_POST)) { 
      require_once \UI\template('/records/edit'); 
		} 
		else { 
      Event::add('success','Record has been updated, thanks!','small'); 
      header('Location:' . Config::get('web_path') . '/records/view/' . scrub_out($record->uid));
	  } 
	break; 
  case 'edit':
    if (!Access::has('record','edit')) { \UI\access_denied(); }
		$record = new Record(\UI\sess::location('objectid')); 
    require_once \UI\template('/records/edit'); 
	break; 
  case 'view':
    if (!Access::has('record','read')) { \UI\access_denied(); }
    $record = new Record(\UI\sess::location('objectid')); 
    require_once \UI\template();
  break;
  case 'new':
    Error::clear(); 
    if (!Access::has('record','create')) { \UI\access_denied(); }
    require_once \UI\template(); 
  break;
  case 'create':
    if (!Access::has('record','create')) { \UI\access_denied(); }
    $_POST['user'] = \UI\sess::$user->uid;
    if ($record_id = Record::create($_POST)) {
      header('Location:' . Config::get('web_path') . '/records/view/' . scrub_out($record_id));
    }
    else {
      require_once \UI\template('/records/new'); 
    }
  break;
  case 'delete': 
    // Admin only
    if (!Access::has('record','delete')) { \UI\access_denied(); }
    // We should do some form ID checking here
    Record::delete($_POST['record_id']);
    header("Location:" . Config::get('web_path') . "/records"); 
    exit; 
  break;
  case 'print': 
    // For now its just tickets
    $ticket = new Content(\UI\sess::location('objectid'),'ticket'); 
    $record = new Record(\UI\sess::location('objectid')); 
//    if (!$ticket->filename OR filemtime($ticket->filename) < $record->updated) { 
      Content::write(\UI\sess::location('objectid'),'ticket',$ticket->filename); 
//    } 
    header("Location:" . Config::get('web_path') . '/media/ticket/' . \UI\sess::location('objectid'));
  break; 
  case 'search':
    if (!Access::has('record','read')) { \UI\access_denied(); }
    // If no post check for get
    if (!isset($_POST['field']) AND !isset($_POST['value'])) { 
      $field = \UI\sess::location('2');
      $value = \UI\sess::location('3');
      if (strlen($field) AND strlen($value)) {
        $_POST['field'] = $field;
        $_POST['value'] = $value;
      }
    }
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_filter($_POST['field'],$_POST['value']); 
    $records = $view->run(); 
    require_once \UI\template('/records/show'); 
  break;
  case 'sort':
    if (!Access::has('record','read')) { \UI\access_denied(); }
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'station_index';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_sort($field,$order); 
    $view->set_start(0); 
    $records = $view->run(); 
    require_once \UI\template('/records/show'); 
  break; 
  case 'offset': 
    if (!Access::has('record','read')) { \UI\access_denied(); }
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_start(\UI\sess::location('objectid')); 
    $records= $view->run(); 
    require_once \UI\template('/records/show'); 
  break;
  default:
    if (!Access::has('record','read')) { \UI\access_denied(); }
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_sort('station_index','ASC');
    $records = $view->run(); 
    require_once \UI\template('/records/show');
  break; 
} // end switch
?>
<?php require_once 'template/footer.inc.php'; ?>
