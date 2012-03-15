<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; }
require_once 'template/menu.inc.php'; 
?>
<table class="data">
<tr>
	<th class="enabled">Status</th>
	<th class="username">Username</th>
	<th class="name">Name</th>
	<th class="access">Access Level</th>
	<th class="action">&nbsp;</th>
	<th class="action">&nbsp;</th>
	<th class="action">&nbsp;</th>
</tr>
<?php foreach ($users as $client) { require 'template/admin_user_row.inc.php'; } ?>
</table> 
