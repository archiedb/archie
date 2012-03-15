<?php if (INIT_LOADED != '1') { exit; } ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us" dir="ltr">
<head>
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/template/base.css" type="text/css" media="screen" />
<script type="text/javascript" language="javascript">
function focus(){ document.login.username.focus(); }
</script>
<script src="<?php echo Config::get('web_path'); ?>/template/ajax.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/template/prototype.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Login </title>
</head>
<body id="loginPage" onload="focus();">
<div id="content"> 
	<div id="header"> 
		<h1>Archie Login</h1>
	</div>
	<div id="loginbox">
		<form name="login" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/login.php">
		<div class="loginfield" id="usernamefield">
		<label for="username">Username:</label>
		<input class="text_input" type="text" id="username" name="username" value="<?php echo scrub_out($_POST['username']); ?>" />
		</div>
		<div class="loginfield" id="passworfield">
		<label for="password">Password:</label>
		<input class="text_input" type="password" id="password" name="password" value="" />
		</div>
		<div class="formValidation">
		<input type="hidden" name="referrer" value="<?php echo scrub_out($_SERVER['HTTP_REFERRER']); ?>" />
		<input class="button" id="loginbutton" type="submit" value="Login" />
		</div>
	</div>

</body>
</html>
