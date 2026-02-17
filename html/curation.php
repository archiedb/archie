<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once '../class/init.php'; 
require_once \UI\template('/header');
// Switch on the action
switch (\UI\sess::location('action')) { 
  case 'upload':
  break;
  case 'image_edit': 
  break; 
  case '3dmodel_edit':
  break;
  case 'image_delete':
  break; 
  case '3dmodel_delete':
  break;
  case 'media_delete':
  break; 
	case 'update': 
	break; 
  case 'edit':
	break; 
  case 'view':
  break;
  case 'new':
    Err::clear(); 
    if (!Access::has('curation','create')) { \UI\access_denied(); }
    require_once \UI\template(); 
  break;
  case 'create':
    if (!Access::has('curation','create')) { \UI\access_denied(); }
    $_POST['user'] = \UI\sess::$user->uid;
    if ($record_id = Curation::create($_POST)) {
      header('Location:' . Config::get('web_path') . '/curation/view/' . scrub_out($record_id));
    }
    else {
      require_once \UI\template('/curation/new'); 
    }
  break;
  case 'delete': 
  break;
  case 'print': 
  break; 
  case 'search':
  break;
  case 'sort':
  break; 
  case 'offset': 
  break;
  default:
    if (!Access::has('curation','read')) { \UI\access_denied(); }
    $view = new View(); 
    $view->reset(); 
    $view->set_type('curation'); 
    $view->set_sort('uid','ASC');
    $records = $view->run(); 
    require_once \UI\template('/curation/show');
  break; 
} // end switch
require_once \UI\template('/footer');
?>
