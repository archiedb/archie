<?php 

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
if (($_POST['username'] && $_POST['password'])) { 
	$username = scrub_in($_POST['username']); 
	$password = $_POST['password']; // Don't scrub this, but we will escape it for the DB
	
	$auth = vauth::authenticate($username,$password);  

	if ($auth['success']) { 
		$username = $auth['username']; 
		$user = User::get_from_username($username); 
		if ($user->disabled) { 
			Error::add('general','Your account is disabled, please contact the administrator for more information'); 
			Error::display('general'); 
			unset($auth); 
			exit; // Escape!
		} 
	} 
	// They didn't get the PW right
	else { 
		Error::add('general','Incorrect Username or Password, please try again'); 
	} 

} 

// Little odd, but this is how I did it in ampache so, check for success and
// actually do the work
if ($auth['success']) { 

	vauth::session_create($auth); 

	$_SESSION['sess_data'] = $auth; 

        header('Location: ' . Config::get('web_path') . '/index.php');
        exit();

} // if successful authentication 

require 'template/login.inc.php'; 
