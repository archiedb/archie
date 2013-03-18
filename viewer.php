<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
if (\UI\sess::$user->access < 100) { exit; } 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'ply': 
  case 'stl':
    $model = new Content(\UI\sess::location('objectid'),'media');
    $info = pathinfo($model->filename); 
    $extension = $info['extension']; 
    require_once \UI\template('/viewer/3dmodels'); 
  break;
}
?>
