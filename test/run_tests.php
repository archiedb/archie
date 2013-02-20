<?php 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
define('NO_LOG',1); 
define('CLI',1); 
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

\Enhance\Core::discoverTests('.');
\Enhance\Core::runTests();


?>
