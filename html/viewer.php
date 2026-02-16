<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'ply': 
  case 'stl':
    $model = new Content(\UI\sess::location('3'),'3dmodel',\UI\sess::location('2'));
    $info = pathinfo($model->filename); 
    $extension = $info['extension']; 
    require_once \UI\template('/viewer/3dmodels'); 
  break;
  default: 
    echo "Nope";
  break;
}
?>
