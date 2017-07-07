<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

// Don't require a session here because this is the login page
define('NO_SESSION','1'); 
require_once 'class/init.php';

/* We have to create the cookie because IIS can't
 * handle the Cookie + Redirect, *sigh*
 */
vauth::create_cookie(); 


// Clean up anything that could be $auth
unset($auth); 


/* Check for posted username and password */
if (isset($_POST['username']) && isset($_POST['password'])) { 
	$username = scrub_in($_POST['username']); 
	$password = $_POST['password']; // Don't scrub this, but we will escape it for the DB
	
	$auth = vauth::authenticate($username,$password);  

	if ($auth['success']) { 
		$username = $auth['username']; 
		$user = User::get_from_username($username); 
		if ($user->disabled) { 
			Err::add('general','Your account is disabled, please contact the administrator for more information'); 
			Err::display('general'); 
			unset($auth); 
			exit; // Escape!
		} 
	} 
	// They didn't get the PW right
	else { 
		Err::add('general','Incorrect Username or Password, please try again'); 
	} 

} 

// Little odd, but this is how I did it in ampache so, check for success and
// actually do the work
if (isset($auth['success'])) { 

	vauth::session_create($auth); 

	$_SESSION['sess_data'] = $auth; 

  // Check for a referrer
  if (isset($_POST['return'])) {
    if (substr($_POST['return'],0,strlen(Config::get('web_path'))) == Config::get('web_path')) {
      $url = \UI\return_url(substr($_POST['return'],strlen(Config::get('web_path'))));
      header('Location: ' . Config::get('web_path') . $url);    
      exit(); 
    }
  }

  header('Location: ' . Config::get('web_path'));
  exit();

} // if successful authentication 

require 'template/login.inc.php'; 
