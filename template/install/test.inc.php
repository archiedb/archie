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
<strong>Step 1: Verify System Configuration</strong><br />
    Before continuing with the Archie installation please make sure that the 
    following tests pass. Your Archie system may not install correctly if any 
    of these tests do not pass. 
  </p>
</div>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Check</th>
        <th>Status</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>PHP PDO Extension</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_php_pdo()); ?></p></td>
        <td>PHP-PDO Database extension must be enabled for Archie to work</td>
      </tr>
      <tr>
        <td>PHP PDO MySQL</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_php_pdomysql()); ?></p></td>
        <td>MySQL Extensions for PHP PDO are enabled</td>
      </tr>
      <tr>
        <td>ImageMagick</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_imagemagick()); ?></p></td>
        <td>ImageMagick Convert</td>
      </tr>
      <tr>
        <td>Python & Required Modules</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_python_scatterplots()); ?></p></td>
        <td>Python + Modules:MySQLdb,os,errno,csv,sys,numpy,matplotlib,ConfigParser</td>
      </tr>
      <tr>
        <td>WWW Directory Writeable</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_root_writeable()); ?></p></td>
        <td>Ensure WWW Directory is writeable by webserver</td>
      </tr>
      <tr>
        <td>Config Directory Writeable</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_config_writeable()); ?></p></td>
        <td>Verify ./config/ directory is writeable by webserver</td>
      </tr>
      <tr>
        <td>Apache Mod-Rewrite Enabled</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_mod_rewrite()); ?></p></td>
        <td>Make sure mod_rewrite is enabled in Apache</td>
      </tr>
      </tr>
    </tbody>
  </table>

  <a href="install.php?action=database" class="btn btn-primary" role="button">Step 2 :: Database Install</a>
  <a href="install.php" class="btn btn-danger" role="button">Recheck</a>

</div> <!-- /container -->
</body>
</html>
