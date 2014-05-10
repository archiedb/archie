<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/krotovina/view/<?php echo scrub_out($krotovina->uid); ?>">
      <?php echo scrub_out($krotovina->record); ?></a>
  </td>
	<td><?php echo scrub_out($krotovina->keywords); ?></td>
	<td><?php echo scrub_out($krotovina->description); ?></td>
  <td>
    <div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/krotovina/edit/<?php echo scrub_out($krotovina->uid); ?>">Edit</a>
    </div>
  </td>
</tr> 
