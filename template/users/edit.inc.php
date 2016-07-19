<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<h3><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<form class="form-horizontal" method="post" action="<?php echo Config::get('web_path'); ?>/users/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" name="name" type="text" id="inputName" placeholder="Display Name" value="<?php \UI\form_value(array('post'=>'name','var'=>$user->name)); ?>">
      <span class="help-inline"><?php echo Err::get('name'); ?></span>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('email'); ?>">
    <label class="col-md-2 control-label" for="inputEmail">Email</label>
    <div class="col-md-2">
      <input class="form-control" name="email" type="text" id="inputEmail" placeholder="Email" value="<?php \UI\form_value(array('post'=>'email','var'=>$user->email)); ?>">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="<?php Err::form_class('site'); ?>">
    <label class="col-md-2 control-label" for="inputSite">Current Site</label>
    <div class="col-md-2">
      <select class="form-control" id="inputSite" name="site">
      <?php
          $sites = $user->get_sites();
          foreach ($sites as $site) {
            $is_selected= ($site->uid == $user->site->uid) ? ' selected="selected"' : '';
            
      ?>
        <option value="<?php $site->_print('uid'); ?>"<?php echo $is_selected; ?>><?php $site->_print('name'); ?></option>
      <?php } ?>
      </select>
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="<?php Err::form_class('password'); ?>">
    <label class="col-md-2 control-label" for="inputPassword">Password</label>
    <div class="col-md-2">
      <input class="form-control" name="password" type="password" id="inputPassword" placeholder="Reset Password...">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="<?php Err::form_class('password'); ?>">
    <label class="col-md-2 control-label" for="inputConfirmPassword">Confirm Password</label>
    <div class="col-md-2">
      <input class="form-control" name="confirmpassword" type="password" id="inputConfirmPassword" placeholder="Confirm Password...">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-2 col-md-offset-2">
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>">
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
  </div>
</form>
</fieldset>
