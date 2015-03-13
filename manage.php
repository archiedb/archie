<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 

require_once 'template/header.inc.php'; 
require_once 'template/menu.inc.php'; 
switch (\UI\sess::location('action')) {
  case 'regenerate':
  if (!Access::is_admin()) { \UI\access_denied(); } 
    // Regenerate what!?
    switch (\UI\sess::location('2')) {
      case 'qrcode':
        $cron = new Cron('qrcode'); 
        $cron->request('all'); 
      break;
      case 'thumbnail': 
        // Add a request for the cron job. 
        $cron = new Cron('thumb');
        $cron->request('all'); 
      break; 
      case '3dmodel_thumb':
        $cron = new Cron('3dmodel_thumb'); 
        $cron->request('all'); 
      break;
      case 'scatterplots':
        $cron = new Cron('scatterplots');
        $cron->request('all');
      break;
    }
    header("Location:" . Config::get('web_path')  . '/manage/tools'); 
  break;
  case 'site':
    switch (\UI\sess::location('2')) {
      case 'add':
        if (!Access::has('site','create')) { \UI\access_denied(); }
        require_once \UI\template('/site/new');
      break;
      case 'create':
        if (!Access::has('site','create')) { \UI\access_denied(); }
        if (!Site::create($_POST)) {
          require_once \UI\template('/site/new');
        }
        else {
          header("Location:" . Config::get('web_path') . '/manage/site/view');
        }
      break;
      case 'edit':
        if (!Access::has('site','edit')) { \UI\access_denied(); }
        $site = new Site(\UI\sess::location('3'));
        require_once \UI\template('/site/edit');
      break;
      case 'update':
        if (!Access::has('site','edit')) { \UI\access_denied(); }
        $site = new Site($_POST['site_uid']);
        if (!$site->update($_POST)) {
          require_once \UI\template('/site/edit');
        }
        else {
          header("Location:" . Config::get('web_path') . '/manage/site/view');
        }
      break;
      case 'view':
      default:
        if (!Access::has('site','read')) { \UI\access_denied(); }
        $sites = Site::get_all();
        require_once \UI\template('/site/view');
      break;
    }
  break;
  case 'import': 
    if (!Access::is_admin()) { \UI\access_denied(); }
    $import = new Import($_POST['type']);   
    $import->run($_FILES['import']['tmp_name']);
    header("Location:" . Config::get('web_path') . '/manage/tools'); 
  break;
  case 'tools':
    if (!Access::is_admin()) { \UI\access_denied(); }
    require_once \UI\template('/manage/tools'); 
  break; 
  case 'material':
    if (!Access::is_admin()) { \UI\access_denied(); }
    // Do what with material?
    switch (\UI\sess::location('2')) {
      case 'edit':
        $material = new Material(\UI\sess::location('3'));
        require_once \UI\template('/material/edit');
      break;
      case 'update':
        $material = new Material($_POST['material_id']);
        if (!$material->update($_POST)) {
          require_once \UI\template('/material/edit');
        }
        else {
          header("Location:" . Config::get('web_path') . '/manage/material/view');
        }
      break;
      case 'add':
        require_once \UI\template('/material/new');
      break;
      case 'create':
        if (!Material::create($_POST)) {
          require_once \UI\template('/material/new');
        } else {
          $materials = Material::get_all(); 
          require_once \UI\template('/material/view');
        }
      break;
      case 'disable':
        $material = new Material(\UI\sess::location('3'));
        $material->disable();
        header("Location:" . Config::get('web_path') . '/manage/material');
      break;
      case 'enable':
        $material = new Material(\UI\sess::location('3')); 
        $material->enable(); 
        header("Location:" . Config::get('web_path') . '/manage/material');
      break;
      case 'view':
      default:
        $materials = Material::get_all(); 
        require_once \UI\template('/material/view'); 
      break;
    }
  break;
  case 'classification':
    if (!Access::is_admin()) { \UI\access_denied(); }
    // Do what?
    switch (\UI\sess::location('2')) { 
      case 'add':
        require_once \UI\template('/classification/new');
      break;
      case 'create':
        if (!Classification::create($_POST)) { 
          require_once \UI\template('/classification/new'); 
        } else {
          $classifications = Classification::get_all();
          require_once \UI\template('/classification/view');
        }
      break;
      case 'enable':
        $classification = new Classification(\UI\sess::location('3'));
        $classification->enable();
        header("Location:" . Config::get('web_path') . '/manage/classification');
      break;
      case 'disable':
        $classification = new Classification(\UI\sess::location('3'));
        $classification->disable();
        header("Location:" . Config::get('web_path') . '/manage/classification');
      break;
      case 'view':
      default:
        $classifications = Classification::get_all(); 
        require_once \UI\template('/classification/view'); 
      break;
    }
  break; 
  case 'group':
    if (!Access::is_admin()) { \UI\access_denied(); }
    switch (\UI\sess::location('2')) { 
      case 'roles':
        $group = new Group(\UI\sess::location('3'));
        require_once \UI\template('/group/role');
      break;
      case 'addrole':
        $group = new Group($_POST['uid']);
        if (!$group->add_role($_POST)) {
          require_once \UI\template('/group/role');
        }
        else {
          header("Location:" . Config::get('web_path') . "/manage/group/roles/" . $group->uid);
          exit;
        }
      break;
      case 'deleterole':
        $group = new Group(\UI\sess::location('4'));
        $group->delete_role(\UI\sess::location('3'));
        header("Location:" . Config::get('web_path') . "/manage/group/roles/" . $group->uid);
        exit;
      case 'new':
        require_once \UI\template('/group/new'); 
      break;
      case 'create':
        if (!Group::create($_POST)) { 
          require_once \UI\template('/group/new');
        }
        else {
          header("Location:" . Config::get('web_path') . "/manage/group");
          exit;
        }
      break;
      case 'edit':
        $group = new Group(\UI\sess::location('3'));
        require_once \UI\template('/group/edit');
      break;
      case 'update':
        $group = new Group($_POST['group']);
        if (!$group->update($_POST)) {
          require_once \UI\template('/group/edit'); 
        }
        else {
          header("Location:" . Config::get('web_path') . '/manage/group');
          exit;
        }
      break;
      case 'view':
      default:
        $groups = Group::get_all();
        require_once \UI\template('/group/show');
      break;
    }
  break;
  default: 
  case 'status':
    if (!Access::is_admin()) { \UI\access_denied(); }
    // Include debug tools 
    require_once 'class/debug.namespace.php';
    require_once \UI\template('/manage/status'); 
  break; 
} // end action switch 

require_once 'template/footer.inc.php'; 

?>
