<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
if (\UI\sess::$user->access < 100) { exit; } 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'regenerate':
    // Regenerate what!?
    switch (\UI\sess::location('objectid')) {
      case 'qrcode':
        $cron = new Cron('qrcode'); 
        $cron->request('all'); 
      break;
      case 'thumbnail': 
        // Add a request for the cron job. 
        $cron = new Cron('thumb');
        $cron->request('all'); 
      break; 
      case '3dmodel_thumb':
        $cron = new Cron('3dmodel_thumb'); 
        $cron->request('all'); 
      break;
    }
    header("Location:" . Config::get('web_path')  . '/manage/tools'); 
  break;
  case 'import': 
    $import = new Import($_POST['type']);   
    $import->run($_FILES['import']['tmp_name']);
    header("Location:" . Config::get('web_path') . '/manage/tools'); 
  break;
  case 'tools':
    require_once \UI\template('/manage/tools'); 
  break; 
  case 'material':
    // Do what with material?
    switch (\UI\sess::location('objectid')) {
      case 'add':
        require_once \UI\template('/material/new');
      break;
      case 'create':
        if (!Material::create($_POST['name'])) {
          require_once \UI\template('/material/new');
        } else {
          $materials = Material::get_all(); 
          require_once \UI\template('/material/view');
        }
      break;
      case 'disable':
        $material = new Material(\UI\sess::location('3'));
        $material->disable();
        header("Location:" . Config::get('web_path') . '/manage/material');
      break;
      case 'enable':
        $material = new Material(\UI\sess::location('3')); 
        $material->enable(); 
        header("Location:" . Config::get('web_path') . '/manage/material');
      break;
      case 'view':
      default:
        $materials = Material::get_all(); 
        require_once \UI\template('/material/view'); 
      break;
    }
  break;
  case 'classification':
    // Do what?
    switch (\UI\sess::location('objectid')) { 
      case 'add':
      
      break;
      case 'disable':
      break;
      case 'view':
      default:
        $classifications = Classification::get_all(); 
        require_once \UI\template('/classification/view'); 
      break;
    }
  break; 
  default: 
  case 'status':
    // Include debug tools 
    require_once 'class/debug.namespace.php';
    require_once \UI\template('/manage/status'); 
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
