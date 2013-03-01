<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="content-block">
<fieldset>
<legend>Add New User</legend>
<?php Error::display('general'); ?>
<form class="form-horizontal" method="post" action="<?php echo Config::get('web_path'); ?>/users/create">
  <div class="control-group<?php Error::display_class('name','required'); ?>">
    <label class="control-label" for="inputName">Name</label>
    <div class="controls">
      <input name="name" type="text" id="inputName" placeholder="Display Name" value="<?php echo scrub_out($_POST['name']); ?>">
      <span class="help-inline"><?php echo Error::get('name'); ?></span>
    </div>
  </div>
  <div class="control-group<?php Error::display_class('username','required'); ?>">
    <label class="control-label" for="inputUsername">Username</label>
    <div class="controls">
      <input name="username" type="text" id="inputUsername" placeholder="Username" value="<?php echo scrub_out($_POST['username']); ?>">
      <span class="help-inline"><?php echo Error::get('username'); ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Email</label>
    <div class="controls">
      <input name="email" type="text" id="inputEmail" placeholder="Email Address" value="<?php echo scrub_out($_POST['email']); ?>">
    </div>
  </div>
  <div class="control-group<?php Error::display_class('password','required'); ?>">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input name="password" type="password" id="inputPassword" placeholder="Password...">
      <span class="help-inline"><?php echo Error::get('password'); ?></span>
    </div>
  </div>
  <div class="control-group<?php Error::display_class('password','required'); ?>">
    <label class="control-label" for="inputConfirmPassword">Confirm Password</label>
    <div class="controls">
      <input name="confirmpassword" type="password" id="inputConfirmPassword" placeholder="Confirm Password...">
      <span class="help-inline"><?php echo Error::get('password'); ?></span>
    </div>
  </div>
  <div class="control-group<?php Error::display_class('access','required'); ?>">
    <label class="control-label" for="inputAccess">Access Level</label>
    <div class="controls">
      <select name="access">
        <option value="0">User</option>
        <option value="50">Manager</option>
        <option value="100">Admin</option>
      </select>
      <span class="help-inline"><?php echo Error::get('access'); ?></span>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn btn-success">Add New User</button>
    </div>
  </div>
</form>
</fieldset>
</div><!-- End content block -->
