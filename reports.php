<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
require_once 'template/header.inc.php'; 

// Switch on the action
switch (\UI\sess::location('action')) { 
  case 'download': 
    $report = new Report(\UI\sess::location('2'),\UI\sess::location('3')); 
    $report->download(\UI\sess::location('4')); 
  break; 
  case 'request':
    $report = new Report(\UI\sess::location('2'),\UI\sess::location('3')); 
    $report->request(\UI\sess::location('4')); 
    require_once \UI\template('/reports/view'); 
  break; 
  default:
    require_once \UI\template('/reports/view'); 
  break;
}
?>
