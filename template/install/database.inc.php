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
  <h2 class="text-center">Archie System Installer :: Step 2 - Verify System</h2>
  <p>
    Before continuing with the Archie installation please make sure that the 
    following tests pass. Your Archie system may not install correctly if any 
    of these tests do not pass. 
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
        <td>PHP PDO MySQL</td>
        <td><p align="center"><?php echo \UI\boolean_word(\Debug\check_php_pdomysql()); ?></p></td>
        <td>MySQL Extensions for PHP PDO are enabled</td>
      </tr>
    </tbody>
  </table>

  <a href="install.php?action=config" class="btn">Step 3 :: Config File</a>

</div> <!-- /container -->
</body>
</html>
