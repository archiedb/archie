<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/debug.namespace.php';
require_once 'class/ui.namespace.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="template/base.css" type="text/css" media="screen" />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
<script src="template/ajax.js" language="javascript" type="text/javascript"></script>
<script src="lib/javascript/jquery-1.9.1.min.js" language="javascript" type="text/javascript"></script>
<script src="lib/bootstrap/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Test Page </title>
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
  <h2 class="text-center">Archie System Test Page</h2>
  <p>
    This page does a series of basic tests of your system, and current configuration. Any tests that
    fail are displayed in red. Yellow indicates that archie should work on your system but some features 
    might not be available. Green indicates that everything is ok, and Archie should work as expected. 
  </p>
  <table class="table table-hover table-bordered table-condensed">
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
        <td>Archie Config</td>
        <td> </td>
        <td>Archie config is readable and contains minimal settings required</td>
      </tr>
      <tr>
        <td>MySQL Connection</td>
        <td> </td>
        <td>MySQL Connection information in the Archie config is correct</td>
      </tr>
      <tr>
        <td>Database Inserted</td>
        <td> </td>
        <td>Basic Database tables exist and are readable with at least one active user</td>
      </tr>
    </tbody>
  </table>
</div> <!-- /container -->
</body>
</html>
