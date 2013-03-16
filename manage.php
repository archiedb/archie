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
  default: 
  case 'status':
    // Include debug tools 
    require_once 'class/debug.namespace.php';
    require_once \UI\template('/manage/status'); 
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
