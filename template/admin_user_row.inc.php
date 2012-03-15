<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
if ($GLOBALS['user']->access < 100) { exit; }
// Few things to setup here
$user_status_class = 'enabled'; 
if ($client->disabled) { $user_status_class = 'disabled'; }
?>
<tr>
	<td class="<?php echo $user_status_class; ?>">&nbsp;</td>
	<td class="username"><?php echo scrub_out($client->username); ?></td>
	<td class="name"><?php echo scrub_out($client->name); ?></td>
	<td class="access"><?php echo scrub_out(User::get_access_name($client->access)); ?></td>
	<td class="action"><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=show_set_user_password&user_id=<?php echo scrub_out($client->uid); ?>">Set Password</a></td>
	<td class="action">
		<?php if ($client->disabled) { ?>
		<a href="<?php echo Config::get('web_path'); ?>/admin.php?action=enable_user&uid=<?php echo scrub_out($client->uid); ?>">Enable</a>
		<?php } else { ?>
		<a href="<?php echo Config::get('web_path'); ?>/admin.php?action=disable_user&uid=<?php echo scrub_out($client->uid); ?>">Disable</a>
		<?php } ?>
	
	</td>
	<td class="action">&nbsp;</td>
</tr>
