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
    require_once \UI\template('/level/edit');
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
  default: 
    // Rien a faire
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
