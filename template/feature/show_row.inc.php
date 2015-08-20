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
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/records/search/feature/<?php echo scrub_out($feature->catalog_id); ?>">Records</a></li>
        <?php if (Access::has('feature','delete',$feature->uid)) { ?>
        <li><a href="#confirmdel_<?php echo scrub_out($feature->uid); ?>" role="button" data-toggle="modal">Delete</a></li>
        <?php } ?>
      </ul>
    </div>
    <?php 
      if (Access::has('record','delete',$feature->uid)) {
        include \UI\template('/feature/modal_confirm_delete');
      }
    ?>
  </td>
</tr> 
