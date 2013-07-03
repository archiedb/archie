<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
    <?php echo scrub_out($material->name); ?>
  </td>
  <td><?php echo scrub_out('true')</td>
	<td><?php echo scrub_out('true'); ?></td>
  <td>
		<div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/material/disable/<?php echo scrub_out($user->uid); ?>">Disable</a>
		</div>
  </td>
</tr>
