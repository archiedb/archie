<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; } 
require_once 'template/menu.inc.php'; 
?>
<h2>Set Password for <?php echo $client->name . ' ('. $client->username .')'; ?></h2>
<form method="post" enctype="multi-part/form-data" action="<?php echo Config::get('web_path'); ?>/admin.php?action=set_user_password">
<input name="password" type="textbox" size="12" />
<input type="hidden" name="uid" value="<?php echo scrub_out($client->uid); ?>" />
<input type="submit" value="Set Password" />
</form>
