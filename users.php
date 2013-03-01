<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'view':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND Access::has('user','write',\UI\sess::location('objectid'))) { 
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
    require_once \UI\template(); 
  break; 
  case 'update': 
    if (!Access::has('user','write',\UI\sess::location('objectid'))) { header('Location:' . Config::get('web_path')); exit; }
    // Make sure they set the password and confirmpassword to the same
    if ($_POST['password'] != $_POST['confirmpassword']) { 
      Error::display('general','Error passwords do not match'); 
      require_once \UI\template('users/edit.inc.php'); 
      break; 
    }
    else {
      $user = new User($_POST['user_id']); 
      $user->update($_POST); 
    }
    require_once \UI\template('users/view.inc.php'); 
  break;
  case 'disable':

  break;
  case 'enable': 
  
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
