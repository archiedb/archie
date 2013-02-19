<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; } 
require_once 'template/menu.inc.php'; 
?>
<div class="content-block">
<fieldset class="record"><legend>Manage Archie</legend>
<ul>
<li><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=show_users">Manage User Accounts</a></li>
</ul>

</fieldset>
</div> 
