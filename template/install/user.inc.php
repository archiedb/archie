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
  <h2 class="text-center">Archie System Installer :: Step 3 - Create Admin User</h2>
  <p>
    <em>This step sets up an initial Administrative user, this user will have full access
    to the system, it can be removed or disabled later</em>
    <?php Error::display('general'); ?>
  </p>
<form class="form-horizontal" id="insert_db" method="post" action="install.php?action=createadmin">
<h4>Admin User Information</h4>
<div class="control-group span8">
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
  <label class="control-label" for="submit"> </label>
  <div class="controls">
    <input type="submit" class="btn btn-primary" value="Create User" tabindex="5" />
  </div>
</div>
</div> <!-- /container -->
</body>
</html>
