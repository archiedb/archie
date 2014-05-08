<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/feature/view/<?php echo scrub_out($feature->uid); ?>">
      <?php echo scrub_out($feature->record); ?></a>
  </td>
	<td><?php echo scrub_out($feature->keywords); ?></td>
	<td><?php echo scrub_out($feature->description); ?></td>
  <td>
    <div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/feature/edit/<?php echo scrub_out($feature->uid); ?>">Edit</a>
    </div>
  </td>
</tr> 
