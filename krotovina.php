<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    require_once \UI\template('/krotovina/new'); 
  break;
  case 'create':
    $krotovina_id = Krotovina::create($_POST);
    if ($krotovina_id) {
      $krotovina = new Krotovina($krotovina_id);
      require_once \UI\template('/krotovina/view');
    }
    else {
      require_once \UI\template('/krotovina/new');
    }
  break;
  case 'view':
    $krotovina = new Krotovina(\UI\sess::location('2'));
    require_once \UI\template('/krotovina/view');
  break;
  case 'edit':
    $krotovina = new Krotovina(\UI\sess::location('2'));
    require_once \UI\template('/krotovina/edit');
  break;
  case 'update':
    $krotovina = new Krotovina($_POST['krotovina_id']);
    if ($krotovina->update($_POST)) {
      Event::add('success','Krotovina has been updated','small');
      require_once \UI\template('/krotovina/view');
    }
    else {
      require_once \UI\template('/krotovina/edit');
    }
  break;
  default: 
    $view = new View();
    $view->reset();
    $view->set_type('krotovina');
    $krotovinas = $view->run();
    require_once \UI\template('/krotovina/show');
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
