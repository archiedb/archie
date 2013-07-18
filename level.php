<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    require_once \UI\template('/level/new'); 
  break;
  case 'create':
    // Attempt to create it
    $level_id = Level::create($_POST);
    if ($level_id) {
      $level = new Level($level_id);
      require_once \UI\template('/level/view');
    }
    else {
      require_once \UI\template('/level/new');
    }
  break;
  case 'view':
    $level = new Level(\UI\sess::location('objectid'));
    require_once \UI\template('/level/view'); 
  break;
  case 'edit':
    $level = new Level(\UI\sess::location('objectid'));
    if (!Access::has('admin','admin',$level->uid) AND $level->closed) {
      require_once \UI\template('/level/view');
    }
    else {
      require_once \UI\template('/level/edit');
    }
  break;
  case 'update':
    $level = new Level($_POST['uid']);
    $_POST['user'] = \UI\sess::$user->uid;
    if (!$level->update($_POST)) { 
      require_once \UI\template('/level/edit');
    }
    else {
      Event::add('success','Level Updated, thanks!','small');
      $level = new Level($_POST['uid']); 
      require_once \UI\template('/level/view');
    }
  break;
  case 'image_edit': 
    if (!Access::has('media','write',$_POST['uid'])) { break; }
    Content::update('image',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_delete':
    if (!Access::has('media','delete',$_POST['uid'])) {  break; }
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
  case 'upload':
    Content::upload($_POST['uid'],$_POST,$_FILES,'level'); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'checkclose':
    $level = new Level(\UI\sess::location('objectid'));
    require_once \UI\template('/level/close');
  break;
  case 'close':
    $level = new Level($_POST['uid']); 
    if ($level->close($_POST)) { 
      require_once \UI\template('/level/view'); 
    }
    else {
      require_once \UI\template('/level/close'); 
    }
  break;
  default: 
    $view = new View(); 
    $view->reset(); 
    $view->set_type('level'); 
    $levels = $view->run(); 
    require_once \UI\template('/level/show');
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
