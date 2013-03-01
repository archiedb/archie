<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="content-block">
<fieldset>
<legend><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</legend>
<form class="form-horizontal" method="post" action="<?php echo Config::get('web_path'); ?>/users/update">
  <div class="control-group">
    <label class="control-label" for="inputName">Name</label>
    <div class="controls">
      <input name="name" type="text" id="inputName" placeholder="Display Name" value="<?php echo scrub_out($user->name); ?>">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Email</label>
    <div class="controls">
      <input name="email" type="text" id="inputEmail" placeholder="Email" value="<?php echo scrub_out($user->email); ?>">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input name="password" type="password" id="inputPassword" placeholder="Reset Password...">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputConfirmPassword">Confirm Password</label>
    <div class="controls">
      <input name="confirmpassword" type="password" id="inputConfirmPassword" placeholder="Confirm Password...">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>">
      <button type="submit" class="btn btn-success">Update</button>
    </div>
  </div>
</form>
</fieldset>
</div><!-- End content block -->
