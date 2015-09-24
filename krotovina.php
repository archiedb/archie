<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    if (!Access::has('krotovina','create')) { \UI\access_denied(); } 
    require_once \UI\template('/krotovina/new'); 
  break;
  case 'upload':
    if (!Access::has('media','create')) { \UI\access_denied(); }
    Content::upload($_POST['krotovina_id'],$_POST,$_FILES,'krotovina');
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return']));
  break; 
  case 'create':
    if (!Access::has('krotovina','create')) { \UI\access_denied(); } 
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
    if (!Access::has('krotovina','delete')) { \UI\access_denied(); }
    $krotovina = new Krotovina($_POST['krotovina_id']);
    if (!$krotovina->uid OR $krotovina->has_records() OR !Access::has('krotovina','delete',\UI\sess::$user->uid)) {
      break;
    }
    $krotovina->delete();
    header('Location:' . Config::get('web_path') . '/krotovina');
  break;
  case 'delpoint':
    if (!Access::has('krotovina','edit')) { \UI\access_denied(); }
    $krotovina = new Krotovina($_POST['krotovina_id']);
    $krotovina->del_point($_POST['uid']);
    header('Location:'  . Config::get('web_path') . '/krotovina/view/' . $krotovina->uid);
    exit;
  break;
  case 'addpoint':
    if (!Access::has('krotovina','edit')) { \UI\access_denied(); }
    $krotovina = new Krotovina($_POST['krotovina_id']);
    $krotovina->add_point($_POST);
    header('Location:'  . Config::get('web_path') . '/krotovina/view/' . $krotovina->uid);
    exit;
  break;
  case 'updatepoint':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
    $krotovina = new Krotovina($_POST['krotovina_id']);
    $krotovina->update_point($_POST);
    \UI\redirect('/krotovina/view/' . $krotovina->uid);
  break;
  case 'view':
    if (!Access::has('krotovina','read')) { \UI\access_denied(); }
    $krotovina = new Krotovina(\UI\sess::location('2'));
    require_once \UI\template('/krotovina/view');
  break;
  case 'edit':
    if (!Access::has('krotovina','edit')) { \UI\access_denied(); }
    $krotovina = new Krotovina(\UI\sess::location('2'));
    require_once \UI\template('/krotovina/edit');
  break;
  case 'update':
    if (!Access::has('krotovina','edit')) { \UI\access_denied(); }
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
    if (!Access::has('krotovina','read')) { \UI\access_denied(); }
    $view = new View();
    $view->set_type('krotovina');
    $view->set_start(\UI\sess::location('objectid'));
    $krotovinas = $view->run();
    require_once \UI\template('/krotovina/show');
  break;
  case 'sort':
    if (!Access::has('krotovina','read')) { \UI\access_denied(); }
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
    if (!Access::has('krotovina','read')) { \UI\access_denied(); }
    $view = new View();
    $view->reset();
    $view->set_type('krotovina');
    $krotovinas = $view->run();
    require_once \UI\template('/krotovina/show');
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
