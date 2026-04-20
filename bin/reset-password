<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
define('NO_LOG',true); 
define('OUTDATED_DATABASE_OK','1');
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
require_once $prefix . '/class/init.php';

if (count($argv) != 3) { 
  echo "Resets the web password for any user\n";
  echo "php ./reset-password.php.inc [username] [password]\n";
  exit;
}

$username = $argv[1];
$password = $argv[2];

// We need a user here so we know which site we're working on, FIXME
// Use a reserved user name? 
\UI\sess::set_user(User::get_from_username('cron')); 
$user = User::get_from_username($username);
$results = $user->set_password($password);

?>
