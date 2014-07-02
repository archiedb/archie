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
      header('Location:' . Config::get('web_path') . '/krotovina/view/' . scrub_out($krotovina_id));
      exit;
    }
    else {
      require_once \UI\template('/krotovina/new');
    }
  break;
  case 'delete':
    $krotovina = new Krotovina($_POST['krotovina_id']);
    if (!$krotovina->uid OR $krotovina->has_records() OR !Access::has('krotovina','delete',\UI\sess::$user->uid)) {
      break;
    }
    $krotovina->delete();
    header('Location:' . Config::get('web_path') . '/krotovina');
  break;
  case 'delpoint':
    $krotovina = new Krotovina($_POST['krotovina_id']);
    $krotovina->del_point($_POST['uid']);
    require_once \UI\template('/krotovina/view');
  break;
  case 'addpoint':
    $krotovina = new Krotovina($_POST['krotovina_id']);
    $krotovina->add_point($_POST);
    require_once \UI\template('/krotovina/view');
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
      header('Location:' . Config::get('web_path') . '/krotovina/view/' . scrub_out($krotovina->uid));
      exit;
    }
    else {
      require_once \UI\template('/krotovina/edit');
    }
  break;
  case 'offset':
    $view = new View();
    $view->set_type('krotovina');
    $view->set_start(\UI\sess::location('objectid'));
    $krotovinas = $view->run();
    require_once \UI\template('/krotovina/show');
  break;
  case 'sort':
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'created';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View();
    $view->set_type('krotovina');
    $view->set_sort($field,$order);
    $view->set_start(0);
    $krotovinas = $view->run();
    require_once \UI\template('/krotovina/show');
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
