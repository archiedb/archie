<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="text-right">
  <a class="btn btn-primary disabled" href="<?php echo Config::get('web_path'); ?>/users/passwordreset">Reset Password</a>
</p>
<div class="content-block">
<fieldset>
<legend><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</legend>
<form method="post" action="#">
<!-- Update user stuff here -->
</form>
</fieldset>
</div><!-- End content block -->
