<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="template/base.css" type="text/css" media="screen" />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
<script src="template/ajax.js" language="javascript" type="text/javascript"></script>
<script src="lib/javascript/jquery-1.9.1.min.js" language="javascript" type="text/javascript"></script>
<script src="lib/bootstrap/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Installer </title>
   <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>

</head>
<body>
<div class="container">
  <h2 class="text-center">Archie System Installer :: Step 2 - Install</h2>
  <p>
    <em>This step installs the database and creates the config file. You will need a username and password with full
    administrative access to the database server as well as a username and password for the initial user.</em> 
    <?php Error::display('general'); ?>
  </p>
<form class="form-horizontal" id="insert_db" method="post" action="install.php?action=insertdb">
<div class="control-group span8">
<h4>Database Connection Information</h4>
  <label class="control-label" for="inputUsername">Username</label>
  <div class="controls">
    <input id="inputUsername" name="username" value="<?php echo scrub_out($_POST['username']); ?>" tabindex="1" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="inputPassword">Password</label>
  <div class="controls">
    <input id="inputPassword" name="password" value="<?php echo scrub_out($_POST['password']); ?>" tabindex="2" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="inputHostname">Hostname</label>
  <div class="controls">
    <input id="inputHostname" name="hostname" value="<?php echo scrub_out($_POST['hostname']); ?>" tabindex="3" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="inputDBName">Database Name</label>
  <div class="controls">
    <input id="inputDBName" name="database" value="<?php echo scrub_out($_POST['database']); ?>" tabindex="4" />
  </div>
</div>
<div class="control-group span8">
<h4>Initial Admin User</h4>
  <label class="control-label" for="inputAdminUsername">Username</label>
  <div class="controls">
    <input id="inputAdminUsername" name="admin_username" value="<?php echo scrub_out($_POST['admin_username']); ?>" tabindex="5" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="inputAdminPassword">Password</label>
  <div class="controls">
    <input id="inputAdminPassword" name="admin_password" value="<?php echo scrub_out($_POST['admin_password']); ?>" tabindex="6" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="inputAdminPasswordC">Confirm Password</label>
  <div class="controls">
    <input id="inputAdminPasswordC" name="admin_pw_confirm" value="<?php echo scrub_out($_POST['admin_pw_confirm']); ?>" tabindex="7" />
  </div>
</div>
<div class="control-group span8">
  <label class="control-label" for="submit"> </label>
  <div class="controls">
    <input type="submit" class="btn btn-primary" value="Install Archie" tabindex="8" />
  </div>
</div>
</div> <!-- /container -->
</body>
</html>
