<?php if (INIT_LOADED != '1') { exit; } ?> 
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/template/base.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Config::get('web_path'); ?>/lib/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" />
<script src="<?php echo Config::get('web_path'); ?>/template/ajax.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/lib/javascript/jquery-1.9.1.min.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo Config::get('web_path'); ?>/lib/bootstrap/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
<title> Archie :: Login </title>
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
<?php
  $user_random = array('one ring to rule them','you will be assimilated','do or do not','I dig','Live long','Archaeologists do it','can you'); 
  $pass_random = array('one ring to bind them','resistance is futile','there is no try','therefor I am','and dig faster','in the dirt','dig it?');

  $int = rand(0,count($user_random)); 
?>

<body>

    <div class="container">

      <form class="form-signin" method="post" enctype="multipart/form-data" action="<?php echo Config::get('web_path'); ?>/login.php">
        <h2 class="form-signin-heading">Archie :: Login</h2>
	<input name="username" type="text" class="input-block-level" placeholder="<?php echo scrub_out($user_random[$int]); ?>">
	<input name="password" type="password" class="input-block-level" placeholder="<?php echo scrub_out($pass_random[$int]); ?>">
        <button class="btn btn-large btn-primary" type="submit">Login</button>
      </form>
    </div> <!-- /container -->
</body>
</html>
