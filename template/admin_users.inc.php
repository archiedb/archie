<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; }
require_once 'template/menu.inc.php'; 
?>
<div class="table-container">
<div class="table-header">
	<div class="table-cell">Username</div>
	<div class="table-cell">Name</div>
	<div class="table-cell">Access Level</div>
	<div class="table-cell">&nbsp;</div>
</div><!-- END table-header --> 
<?php foreach ($users as $client) { require 'template/admin_user_row.inc.php'; } ?>
</div><!-- End table container --> 
