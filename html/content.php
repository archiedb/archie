<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'update':
    // Make sure they have access
    if (!Access::has('media','edit')) { \UI\access_denied(); }
    Content::update(\UI\sess::location('object'),$_POST['uid'],$_POST);
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'delete':
    if (!Access::has('media','delete')) { \UI\access_denied(); }
    $media = new Content($_POST['uid'],\UI\sess::location('object'),$_POST['parent']);
    if (!$media->delete()) {
      Event::error('DELETE','Unable to delete media item:' . $media->filename);
      Err::add('delete','Unable to delete 3d Model');
    }
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  default: 
    // Rien a faire
  break; 
} // end action switch 

require_once \UI\template('/footer');

?>
