<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<div class="table-row">
<!-- <tr> --> 
	<div class="table-cell">
		<a href="<?php echo Config::get('web_path'); ?>/new.php?action=edit&record_id=<?php echo scrub_out($record->uid); ?>">
		<?php echo scrub_out($record->site . '-' . $record->catalog_id); ?>
		</a>
	</div>
	<div class="table-cell"><?php echo scrub_out($record->station_index); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->unit); ?></div>
	<div class="table-cell"><?php echo scrub_out(quad::$values[$record->quad]); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->level); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->feature); ?></div>
	<div class="table-cell"><?php echo scrub_out(lsgunit::$values[$record->lsg_unit]); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->material->name); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->classification->name); ?></div>
	<div class="table-cell"><?php echo scrub_out(date("m/d/y",$record->created)); ?></div>
	<div class="table-cell"><?php echo scrub_out($record->user->username); ?></div>
<!--
ACTIONS CRAP make this a select??? 
		<?php if ($GLOBALS['user']->access >= 100) { ?>
	<td>
		<span class="action" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/admin.php?action=confirm_delete&record_id=<?php echo scrub_out($record->uid); ?>';" />
		<img alt="Delete" title="Delete" src="<?php echo Config::get('web_path'); ?>/images/icons/delete.png" /></span>
	</td>
		<?php } ?>
	<td>
		<span class="action" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/new.php?action=edit&record_id=<?php echo scrub_out($record->uid); ?>'" />
		<img alt="Edit" title="Edit" src="<?php echo Config::get('web_path'); ?>/images/icons/application_form_edit.png" />
		</span>
	</td>
-->
</div> <!-- End of table row --> 
