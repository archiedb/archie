<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="template/base.css" type="text/css" media="screen">
  <link rel="stylesheet" href="lib/bootstrap-3/css/bootstrap.min.css" />
  <script src="template/ajax.js" language="javascript" type="text/javascript"></script>
  <script src="lib/javascript/jquery-1.11.3.min.js" language="javascript" type="text/javascript"></script>
  <script src="lib/bootstrap-3/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Installer </title>
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
<div class="container theme-showcase" role="main">
<div class="jumbotron">
  <h1>Archie Installer</h1>
  <p>
    <strong>Step 2: Installation</strong><br />
    This step installs the database and creates the config file. You will need a username and password with full
    administrative access to the database server as well as a username and password for the initial user.
  </p>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="insert_db" method="post" action="install.php?action=insertdb">
<h4>Database Connection Information</h4>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputUsername">Username</label>
  <div class="col-sm-10">
    <input id="inputUsername" name="username" value="<?php echo scrub_out($_POST['username']); ?>" tabindex="1" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputPassword">Password</label>
  <div class="col-sm-10">
    <input id="inputPassword" name="password" value="<?php echo scrub_out($_POST['password']); ?>" tabindex="2" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputHostname">Hostname</label>
  <div class="col-sm-10">
    <input id="inputHostname" name="hostname" value="<?php echo scrub_out($_POST['hostname']); ?>" tabindex="3" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputDBName">Database Name</label>
  <div class="col-sm-10">
    <input placeholder="A-Z,0-9,_,-" id="inputDBName" name="database" value="<?php echo scrub_out($_POST['database']); ?>" tabindex="4" />
  </div>
</div>
<h4>Initial Admin User</h4>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputAdminUsername">Username</label>
  <div class="col-sm-10">
    <input id="inputAdminUsername" name="admin_username" value="<?php echo scrub_out($_POST['admin_username']); ?>" tabindex="5" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputAdminPassword">Password</label>
  <div class="col-sm-10">
    <input id="inputAdminPassword" name="admin_password" value="<?php echo scrub_out($_POST['admin_password']); ?>" tabindex="6" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label" for="inputAdminPasswordC">Confirm Password</label>
  <div class="col-sm-10">
    <input id="inputAdminPasswordC" placeholder="Confirm Password" name="admin_pw_confirm" value="<?php echo scrub_out($_POST['admin_pw_confirm']); ?>" tabindex="7" />
  </div>
</div>
<div class="form-group span8">
  <label class="control-label" for="submit"> </label>
  <div class="controls">
    <input type="submit" class="btn btn-primary" value="Install Archie" tabindex="8" />
  </div>
</div>
</div> <!-- /container -->
</body>
</html>
