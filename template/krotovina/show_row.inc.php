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
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/records/search/krotovina/<?php echo scrub_out($krotovina->catalog_id); ?>">Records</a></li>
        <?php if (Access::has('krotovina','delete')) { ?>
        <li><a href="#confirmdel_<?php echo scrub_out($krotovina->uid); ?>" role="button" data-toggle="modal">Delete</a></li>
        <?php } ?>
      </ul>
    </div>
    <?php 
      if (Access::has('krotovina','delete')) {
        include \UI\template('/krotovina/modal_delete_confirm');
      }
    ?>
  </td>
</tr> 
