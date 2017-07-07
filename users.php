<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'view':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','read')) { 
      Event::error('DENIED','User ' . \UI\sess::$user->username . ' attempted to view someone elses profile!'); 
      header('Location:' . Config::get('web_path')); 
      exit;
    }
    $user = new User(\UI\sess::location('objectid')); 
    require_once \UI\template(); 
  break;
  case 'edit':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','edit')) { \UI\access_denied(); }
    $user = new User(\UI\sess::location('objectid'));
    require_once \UI\template(); 
  break; 
  case 'update': 
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','edit')) { \UI\access_denied(); }
    // Make sure they set the password and confirmpassword to the same
    $retval = true; 
    $user = new User($_POST['uid']); 
    if (!$user->update($_POST)) { 
      $retval = false;
    }
    else { 
      Event::add('success','User information updated');
    }
    // Only reset the password if they typed something in!
    if (!empty($_POST['password'])) {
      if (!$user->set_password($_POST['password'])) {
        Err::add('general','Unable to update password');
        $retval = false;
      }
    }
    if ($retval == true) {
      $user->refresh();  
      \UI\redirect('/users/view/' . $user->uid);
    }
    else { 
      require_once \UI\template('/users/edit');
    }
  break;
  case 'site':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','edit')) { \UI\access_denied('User Edit Permissions Required'); }
    $user = \UI\sess::$user;
    require_once \UI\template('/users/view_site');
  break;
  case 'siteupdate':
    if (\UI\sess::$user->uid != \UI\sess::location('objectid') AND !Access::has('user','edit')) { \UI\access_denied('User Edit Permissions Required'); }
    //FIXME: make sure they already have access to this site
    \UI\sess::$user->update_site(\UI\sess::location('objectid'));
    $user = \UI\sess::$user;
    require_once \UI\template('/users/view_site');
  break;
  case 'disable':
    if (!Access::has('user','manage')) { \UI\access_denied(); }
    // You aren't allowed to disable yourself - sorry!
    if ($_POST['uid'] == \UI\sess::$user->uid) { \UI\access_denied('Unable to disable yourself'); }
    $user = new User($_POST['uid']); 
    $user->disable(); 
    $user->refresh(); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break;
  case 'enable': 
    if (!Access::has('user','manage')) { \UI\access_denied(); }
    $user = new User($_POST['uid']); 
    $user->enable(); 
    $user->refresh(); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break; 
  case 'manage':
    if (!Access::has('user','manage')) { \UI\access_denied(); }
    $filter = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'enabled';
    $users = User::get($filter); 
    require_once \UI\template(); 
  break;   
  case 'add': 
    if (!Access::has('user','create')) { \UI\access_denied(); }
    require_once \UI\template(); 
  break; 
  case 'create': 
    if (!Access::has('user','create')) { \UI\access_denied(); }
    $uid = User::create($_POST);  
    if (!$uid) { 
      require_once \UI\template('/users/add'); 
      break;
    }
    $user = new User($uid); 
    require_once \UI\template('/users/view'); 
  break; 
  case 'permissions':
    if (!Access::has('user','manage')) { \UI\access_denied(); }
    switch (\UI\sess::location('2')) {
      case 'addgroup':
        $user = new User($_POST['uid']);
        $user->add_group($_POST['group']);
        header("Location:" . Config::get('web_path') . "/users/permissions/view/" . $user->uid);
        exit;
      break;
      case 'delgroup':
        $user = new User($_POST['uid']); 
        $user->delete_group($_POST['group']);
        header("Location:" . Config::get('web_path') . "/users/permissions/view/" . $user->uid);
        exit;
      break;
      case 'view':
      default:
        $user = new User(\UI\sess::location('3'));
        require_once \UI\template('/users/permissions/view');
      break;
    } // end switch on 2nd options
  break;
} // end action switch 

require_once \UI\template('/footer');

?>
