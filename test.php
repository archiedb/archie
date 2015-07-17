<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/debug.namespace.php';
require_once 'class/ui.namespace.php';
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
  <title> Archie :: Test </title>
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
<div class="container">
<div class="jumbotron">
  <h1>Archie Test Page</h2>
  <p>
    This page does a series of basic tests of your system, and current configuration. Any tests that
    fail are displayed in red. Yellow indicates that archie should work on your system but some features 
    might not be available. Green indicates that everything is ok, and Archie should work as expected. 
  </p>
</div>
  <table class="table table-hover table-striped">
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
        <td>MySQL</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_php_pdomysql()); ?></p></td>
        <td>MySQL Extensions for PHP PDO are enabled</td>
      </tr>
      <tr>
        <td>Archie Config Readable</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_archie_config_readable()); ?></p></td>
        <td>Archie config is readable by webserver</td>
      </tr>
      <tr>
        <td>Archie Config Valid</td>
        <?php 
          $config_valid = true;
          $fields = \Debug\check_archie_config();
          if (strlen($fields)) { $fields = "False"; $config_valid = false; }
        ?>
        <td><p align="center"><?php echo \UI\boolean_word($config_valid,$fields); ?></p></td>
        <td>Archie config has the minimum required settings</td>
      </tr>
      <tr>
        <td>MySQL Connection</td>
        <?php
          $db_valid = true; 
          $msg = \Debug\check_mysql_config();
          if (strlen($msg)) { $db_valid = false; }
        ?>
        <td><p align="center"><?php echo \UI\boolean_word($db_valid,$msg); ?></p></td>
        <td>MySQL Connection information in the Archie config is correct</td>
      </tr>
      <tr>
        <td>Database Inserted</td>
        <?php 
          $db_inserted = true;
          $msg = \Debug\check_mysql_db();
          if (strlen($msg)) { $db_inserted = false; }
        ?>
        <td><p align="center"><?php echo \UI\boolean_word($db_inserted,$msg); ?></td>
        <td>Basic Database tables exist and are readable with at least one active user</td>
      </tr>
      <tr>
        <td>Apache Rewrite</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_mod_rewrite()); ?></td>
        <td>Check to see if Apache's Mod_rewrite is enabled, this check is invalid on CGI based installations</td>
      </tr> 
    </tbody>
  </table>
</div> <!-- /container -->
</body>
</html>
