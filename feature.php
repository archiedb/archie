<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'new':
    if (!Access::has('feature','create')) { \UI\access_denied(); }
    require_once \UI\template('/feature/new'); 
  break;
  case 'create':
    if (!Access::has('feature','create')) { \UI\access_denied(); }
    $feature_id = Feature::create($_POST);
    if ($feature_id) {
      $feature = new Feature($feature_id);
      \UI\redirect('/feature/view/' . $feature_id);
    }
    else {
      require_once \UI\template('/feature/new');
    }
  break;
  case 'delete':
    if (!Access::has('feature','delete')) { \UI\access_denied(); }
    $feature = new Feature($_POST['feature_id']);
    if (!$feature->uid OR $feature->has_records()) {
      Event::error('Feature still has records');
      require_once \UI\template('/feature/view');
      break;
    }
    $feature->delete();
    \UI\redirect('/feature');
  break;
  case 'delpoint':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
    $feature = new Feature($_POST['feature_id']);
    $feature->del_point($_POST['uid']);
    \UI\redirect('/feature/view' . $feature->uid);
  break;
  case 'addpoint':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
    $feature = new Feature($_POST['feature_id']);
    $feature->add_point($_POST);
    \UI\redirect('/feature/view/' . $feature->uid);
  break;
  case 'updatepoint':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
    $feature = new Feature($_POST['feature_id']);
    $feature->update_point($_POST);
    \UI\redirect('/feature/view/' . $feature->uid);
  case 'view':
    if (!Access::has('feature','read')) { \UI\access_denied(); }
    $feature = new Feature(\UI\sess::location('2'));
    require_once \UI\template('/feature/view');
  break;
  case 'edit':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
    $feature = new Feature(\UI\sess::location('2'));
    require_once \UI\template('/feature/edit');
  break;
  case 'update':
    if (!Access::has('feature','edit')) { \UI\access_denied(); }
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
    if (!Access::has('feature','read')) { \UI\access_denied(); }
    $view = new View();
    $view->set_type('feature');
    $view->set_start(\UI\sess::location('objectid'));
    $features = $view->run();
    require_once \UI\template('/feature/show');
  break;
  case 'sort':
    if (!Access::has('feature','read')) { \UI\access_denied(); }
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
    if (!Access::has('feature','read')) { \UI\access_denied(); }
    $view = new View();
    $view->reset();
    $view->set_type('feature');
    $features = $view->run();
    require_once \UI\template('/feature/show');
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
