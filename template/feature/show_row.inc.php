<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/feature/view/<?php echo scrub_out($feature->uid); ?>">
      <?php $feature->_print('record') ?></a>
  </td>
	<td><?php $feature->_print('keywords'); ?></td>
	<td><?php $feature->_print('description'); ?></td>
  <td>
    <div class="btn-group">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/feature/<?php echo scrub_out($feature->catalog_id); ?>">Records</a>
      <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <?php if (Access::has('feature','edit',$feature->uid)) { ?>
        <li><a href="<?php echo Config::get('web_path'); ?>/feature/edit/<?php echo scrub_out($feature->uid); ?>">Edit</a></li>
        <?php } ?>
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
