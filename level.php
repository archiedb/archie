<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    if (!Access::has('level','create')) { \UI\access_denied(); }
    require_once \UI\template('/level/new'); 
  break;
  case 'create':
    if (!Access::has('level','create')) { \UI\access_denied(); }
    // Attempt to create it
    $level_id = Level::create($_POST);
    if ($level_id) {
      \UI\redirect('/level/view/' . $level_id);
    }
    else {
      require_once \UI\template('/level/new');
    }
  break;
  case 'delete':
    if (!Access::has('level','delete')) { \UI\access_denied(); }
    $level = new Level($_POST['level_id']);
    if (!$level->uid OR $level->has_records()) {
      break;
    }
    $level->delete();
    header('Location:' . Config::get('web_path') . '/level');
  break;
  case 'view':
    if (!Access::has('level','read')) { \UI\access_denied(); }
    $level = new Level(\UI\sess::location('objectid'));
    require_once \UI\template('/level/view'); 
  break;
  case 'edit':
    if (!Access::has('level','edit')) { \UI\access_denied(); }
    $level = new Level(\UI\sess::location('objectid'));
    require_once \UI\template('/level/edit');
  break;
  case 'update':
    if (!Access::has('level','edit')) { \UI\access_denied(); }
    $level = new Level($_POST['uid']);
    $_POST['user'] = \UI\sess::$user->uid;
    $_POST['uid'] = $level->uid;
    if (!$level->update($_POST)) { 
      require_once \UI\template('/level/edit');
    }
    else {
      Event::add('success','Level Updated, thanks!','small');
      $level = new Level($_POST['uid']); 
      require_once \UI\template('/level/view');
    }
  break;
  case 'report':
    if (!Access::has('level','read')) { \UI\access_denied(); }
    $level = new Level(\UI\sess::location('objectid'));
    $report = new Content(\UI\sess::location('objectid'),'level'); 
    Content::write(\UI\sess::location('objectid'),'level','','','','level'); 
  break;
  case 'image_primary':
    if (!Access::has('level','edit')) { \UI\access_denied(); }
    $level = new Level($_POST['uid']); 
    if ($level->set_primary_image($_POST['image'])) { 
      Event::add('success','Level Image Selected','small'); 
    }
    else {
      Error::add('level_image','Unable to set level image'); 
    }
    require_once \UI\template('/level/edit'); 
  break;
  case 'image_edit': 
    if (!Access::has('media','edit')) { \UI\access_denied(); }
    Content::update('image',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_delete':
    if (!Access::has('media','delete')) { \UI\access_denied(); }
    $image = new Content($_POST['uid'],'image','level'); 
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
    if (!Access::has('media','create')) { \UI\access_denied(); }
    Content::upload($_POST['uid'],$_POST,$_FILES,'level'); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'checkclose':
    if (!Access::has('level','edit')) { \UI\access_denied(); }
    $level = new Level(\UI\sess::location('objectid'));
    require_once \UI\template('/level/close');
  break;
  case 'close':
    if (!Access::has('level','edit')) { \UI\access_denied(); }
    $level = new Level($_POST['uid']); 
    if ($level->close($_POST)) { 
      Event::add('success','Level Closed'); 
      header('Location:' . Config::get('web_path') . '/level/view/' . scrub_out($level->uid));
      break;
    }
    else {
      require_once \UI\template('/level/close'); 
    }
  break;
  case 'reopen_level':
    if (!Access::has('level','reopen')) { \UI\access_denied(); }
    $level = new Level($_POST['uid']);
    $level->open();
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'offset':
    if (!Access::has('level','read')) { \UI\access_denied(); }
    $view = new View();
    $view->set_type('level');
    $view->set_start(\UI\sess::location('objectid'));
    $levels = $view->run();
    require_once \UI\template('/level/show');
  break;
  case 'sort':
    if (!Access::has('level','read')) { \UI\access_denied(); }
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'record';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View(); 
    $view->set_type('level'); 
    $view->set_sort($field,$order); 
    $view->set_start(0); 
    $levels = $view->run(); 
    require_once \UI\template('/level/show'); 
  break;
  default: 
    if (!Access::has('level','read')) { \UI\access_denied(); }
    $view = new View(); 
    $view->reset(); 
    $view->set_type('level'); 
    $view->set_sort('unit','ASC');
    $levels = $view->run(); 
    require_once \UI\template('/level/show');
  break; 
} // end action switch 

require_once \UI\template('/footer');

?>
