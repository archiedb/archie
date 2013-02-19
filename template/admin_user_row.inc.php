<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; }
?>
<div class="table-row">
	<div class="table-cell"><?php echo scrub_out($client->username); ?></div>
	<div class="table-cell"><?php echo scrub_out($client->name); ?></div>
	<div class="table-cell"><?php echo scrub_out(User::get_access_name($client->access)); ?></div>
	<div class="table-cell">
		<div class="btn-group">
			<a class="btn" href="<?php echo Config::get('web_path'); ?>/admin.php?action=show_set_user_password&user_id=<?php echo scrub_out($client->uid); ?>">Set Password</a>
			<a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Enable</a></li>
			 	<li><a href="#" rel="nofollow">Disable</a></li>
			</ul>
		</div>
	</select>
</div>
<!--		<?php if ($client->disabled) { ?>
		<a href="<?php echo Config::get('web_path'); ?>/admin.php?action=enable_user&uid=<?php echo scrub_out($client->uid); ?>">Enable</a>
		<?php } else { ?>
		<a href="<?php echo Config::get('web_path'); ?>/admin.php?action=disable_user&uid=<?php echo scrub_out($client->uid); ?>">Disable</a>
		<?php } ?>
-->	
</div>
