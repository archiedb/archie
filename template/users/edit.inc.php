<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<fieldset>
<legend><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</legend>
<?php Error::display('general'); ?>
<form class="form-horizontal" method="post" action="<?php echo Config::get('web_path'); ?>/users/update">
  <div class="control-group<?php Error::display_class('name'); ?>">
    <label class="control-label" for="inputName">Name</label>
    <div class="controls">
      <input name="name" type="text" id="inputName" placeholder="Display Name" value="<?php echo scrub_out($user->name); ?>">
      <span class="help-inline"><?php echo Error::get('name'); ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Email</label>
    <div class="controls">
      <input name="email" type="text" id="inputEmail" placeholder="Email" value="<?php echo scrub_out($user->email); ?>">
    </div>
  </div>
  <div class="control-group<?php Error::display_class('password'); ?>">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input name="password" type="password" id="inputPassword" placeholder="Reset Password...">
      <span class="help-inline"><?php echo Error::get('password'); ?></span>
    </div>
  </div>
  <div class="control-group<?php Error::display_class('password'); ?>">
    <label class="control-label" for="inputConfirmPassword">Confirm Password</label>
    <div class="controls">
      <input name="confirmpassword" type="password" id="inputConfirmPassword" placeholder="Confirm Password...">
      <span class="help-inline"><?php echo Error::get('password'); ?></span>
    </div>
  </div>
  <?php if (Access::has('user','admin')) { ?>
  <div class="control-group<?php Error::display_class('access'); ?>">
    <label class="control-label" for="inputAccessSelect">Access Level</label>
    <div class="controls">
      <select name="access">
        <?php 
          foreach (Access::get_levels() as $value=>$name) { 
            $selected = ''; 
            if ($user->access == $value) { $selected = ' selected="selected"'; }  
        ?>
        <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <?php } ?>
  <div class="control-group">
    <div class="controls">
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>">
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
  </div>
</form>
</fieldset>
