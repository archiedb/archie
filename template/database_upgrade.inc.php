<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; } 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/template/base.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/lib/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
  <script src="<?php echo Config::get('web_path'); ?>/template/ajax.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-1.11.3.min.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo Config::get('web_path'); ?>/lib/bootstrap-3/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Database Upgrade </title>
   <style type="text/css">
    body {
      padding-top: 70px;
      padding-bottom: 30px;
    }

    .theme-dropdown .dropdown-menu {
      position: static;
      display: block;
      margin-bottom: 20px;
    }

    .theme-showcase > p > .btn {
      margin: 5px 0;
    }

    .theme-showcase .navbar .container {
      width: auto;
    }
    </style>
</head>
<body>
<div class="container theme-showcase" role="main">
<div class="jumbotron">
	<h1>Database Upgrade</h1>
	<p>
	This page handles all database updates to Archie. Before continuing please backup your database. 
	Once the upgrade has started do not cancel it, the upgrade may take a while depending on your system.
	Please see below for details on the upgrade which need to be performed on this system. You must enter
	the username and password of an administrator in order to start the database upgrade. 
	</p>
</div>
  <?php Event::display('errors'); ?>
	<form class="form-inline text-center" action="<?php echo Config::get('web_path'); ?>/upgrade.php" method="post">
		<input name="username" type="text" class="input-small" placeholder="Username">
		<input name="password" type="password" class="input-small" placeholder="Password">
		<input type="hidden" name="action" value="database" />
 		<button type="submit" class="btn btn-danger">Upgrade Database</button>
	</form>
	<hr />
	<?php foreach (\update\Database::get_versions('new') as $updates) { ?>
	<p><strong>Version: <?php echo scrub_out($updates['version']); ?></strong><br />
	<em><?php echo $updates['description']; ?></em>
	</p>
	<?php } ?>
</div> <!-- /container -->
</body>
</html>
