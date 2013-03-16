<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'view':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','write',\UI\sess::location('objectid'))) { 
      Event::error('DENIED','User ' . \UI\sess::$user->username . ' attempted to view someone elses profile!'); 
      header('Location:' . Config::get('web_path')); 
      exit;
    }
    $user = new User(\UI\sess::location('objectid')); 
    require_once \UI\template(); 
  break;
  case 'edit':
    // Make sure they are allowed
    if (!Access::has('user','write',\UI\sess::location('objectid'))) { header('Location:' . Config::get('web_path')); exit; }
    $user = new User(\UI\sess::location('objectid'));
    require_once \UI\template(); 
  break; 
  case 'update': 
    if (!Access::has('user','write',\UI\sess::location('objectid'))) { header('Location:' . Config::get('web_path')); exit; }
    // Make sure they set the password and confirmpassword to the same
    $user = new User($_POST['uid']); 

    if (!$user->update($_POST)) { 
      require_once \UI\template('/users/edit'); 
      break;
    }

    // Only reset the password if they typed something in!
    if (strlen($_POST['password'])) { $user->set_password($_POST['password']); }
    // Refresh!
    $user->refresh();  
    require_once \UI\template('/users/view'); 
  break;
  case 'disable':
    if (!Access::has('user','delete',$_POST['uid'])) { header('Location:' . Config::get('web_path')); exit; }
    // You aren't allowed to disable yourself - sorry!
    if ($_POST['uid'] == \UI\sess::$user->uid) { header('Location:' . Config::get('web_path')); exit; }
    $user = new User($_POST['uid']); 
    $user->disable(); 
    $user->refresh(); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'enable': 
    if (!Access::has('user','delete',$_POST['uid'])) { header('Location:' . Config::get('web_path')); exit; }
    $user = new User($_POST['uid']); 
    $user->enable(); 
    $user->refresh(); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break; 
  case 'manage':
    if (!Access::has('user','delete')) { header('Location:' . Config::get('web_path')); exit; }
    $filter = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'enabled';
    $users = User::get($filter); 
    require_once \UI\template(); 
  break;   
  case 'add': 
    if (!Access::has('user','admin')) { header('Location:' . Config::get('web_path')); exit; }
    require_once \UI\template(); 
  break; 
  case 'create': 
    if (!Access::has('user','admin')) { header('Location:' . Config::get('web_path')); exit; }
    $uid = User::create($_POST);  
    if (!$uid) { 
      require_once \UI\template('/users/add'); 
      break; 
    }
    $user = new User($uid); 
    require_once \UI\template('/users/view'); 
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
