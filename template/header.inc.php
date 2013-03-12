<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
  <!-- Le styles -->
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/template/base.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/lib/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/lib/bootstrap/css/bootstrap-fileupload.min.css" type="text/css" media="screen" />
<script src="<?php echo Config::get('web_path'); ?>/template/ajax.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-1.9.1.min.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/lib/bootstrap/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/lib/bootstrap/js/bootstrap-fileupload.min.js" language="javascript" type="text/javascript"></script>
</head>
<body>
