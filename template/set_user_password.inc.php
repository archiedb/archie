<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; } 
require_once 'template/menu.inc.php'; 
?>
<div class="content-block">
<form method="post" enctype="multi-part/form-data" action="<?php echo Config::get('web_path'); ?>/admin.php?action=set_user_password">
<fieldset>
<legend>Reset Password for <?php echo scrub_out($client->name . ' ('. $client->username .')'); ?></legend>
<input placeholder="Password..." name="password" type="textbox" size="12" />
<input type="hidden" name="uid" value="<?php echo scrub_out($client->uid); ?>" />
<button type="submit" class="btn btn-primary">Reset Password</button>
</fieldset>
</form>
</div>
