<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td><?php echo scrub_out($site->name); ?></td>
  <td><?php echo scrub_out($site->description); ?></td>
  <td><?php echo scrub_out($site->pi); ?></td>
	<td><?php echo \UI\boolean_word($site->enabled); ?></td>
  <td>
		<div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/site/edit/<?php echo scrub_out($site->uid); ?>">Edit</a>
		</div>
  </td>
</tr>
