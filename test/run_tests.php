<?php 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
define('NO_LOG',1); 
define('CLI',1); 
define('UNIT_TEST',1);
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/test/php/data.php';
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

// Create an initial user
User::create(array('name'=>'Test','username'=>'tester','email'=>'a@a.com','password'=>'test','confirmpassword'=>'test','site'=>'1'));

// We need to make it look like a real session
\UI\sess::set_user(new User(1)); 

\Enhance\Core::discoverTests($prefix . '/test/php');
\Enhance\Core::runTests();


?>
