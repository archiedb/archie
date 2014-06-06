<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    require_once \UI\template('/feature/new'); 
  break;
  case 'create':
    $feature_id = Feature::create($_POST);
    if ($feature_id) {
      $feature = new Feature($feature_id);
      require_once \UI\template('/feature/view');
    }
    else {
      require_once \UI\template('/feature/new');
    }
  break;
  case 'delpoint':
    $feature = new Feature($_POST['feature_id']);
    $feature->del_point($_POST['uid']);
    require_once \UI\template('/feature/view');
  break;
  case 'addpoint':
    $feature = new Feature($_POST['feature_id']);
    $feature->add_point($_POST);
    require_once \UI\template('/feature/view');
  break;
  case 'view':
    $feature = new Feature(\UI\sess::location('2'));
    require_once \UI\template('/feature/view');
  break;
  case 'edit':
    $feature = new Feature(\UI\sess::location('2'));
    require_once \UI\template('/feature/edit');
  break;
  case 'update':
    $feature = new Feature($_POST['feature_id']);
    if ($feature->update($_POST)) {
      Event::add('success','Feature has been updated','small');
      require_once \UI\template('/feature/view');
    }
    else {
      require_once \UI\template('/feature/edit');
    }
  break;
  case 'offset':
    $view = new View();
    $view->set_type('feature');
    $view->set_start(\UI\sess::location('objectid'));
    $features = $view->run();
    require_once \UI\template('/feature/show');
  break;
  case 'sort':
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'created';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View();
    $view->set_type('feature');
    $view->set_sort($field,$order);
    $view->set_start(0);
    $features = $view->run();
    require_once \UI\template('/feature/show');
  break;
  default: 
    $view = new View();
    $view->reset();
    $view->set_type('feature');
    $features = $view->run();
    require_once \UI\template('/feature/show');
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
