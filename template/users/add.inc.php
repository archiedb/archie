<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <h3>Add new User</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" method="post" action="<?php echo Config::get('web_path'); ?>/users/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" name="name" type="text" id="inputName" placeholder="Display Name" value="<?php \UI\form_value('name'); ?>">
    </div>
    </div>
  </div>
  <div class="form-group<?php Err::display_class('username','required'); ?>">
    <div class="<?php Err::form_class('username'); ?>">
    <label class="col-md-2 control-label" for="inputUsername">Username</label>
    <div class="col-md-2">
      <input class="form-control" name="username" type="text" id="inputUsername" placeholder="Username" value="<?php \UI\form_value('username'); ?>">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="<?php Err::form_class('email'); ?>">
    <label class="col-md-2 control-label" for="inputEmail">Email</label>
    <div class="col-md-2">
      <input class="form-control" name="email" type="text" id="inputEmail" placeholder="Email Address" value="<?php \UI\form_value('email'); ?>">
    </div>
    </div>
  </div>
  <div class="form-group<?php Err::display_class('password','required'); ?>">
    <div class="<?php Err::form_class('password'); ?>">
    <label class="col-md-2 control-label" for="inputPassword">Password</label>
    <div class="col-md-2">
      <input class="form-control" name="password" type="password" id="inputPassword" placeholder="Password...">
    </div>
    </div>
  </div>
  <div class="form-group<?php Err::display_class('password','required'); ?>">
    <div class="<?php Err::form_class('password'); ?>">
    <label class="col-md-2 control-label" for="inputConfirmPassword">Confirm Password</label>
    <div class="col-md-2">
      <input class="form-control" name="confirmpassword" type="password" id="inputConfirmPassword" placeholder="Confirm Password...">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-2 col-md-offset-2">
      <button type="submit" class="btn btn-success">Add New User</button>
    </div>
  </div>
</form>
