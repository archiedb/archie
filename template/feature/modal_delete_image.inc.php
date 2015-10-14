<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_delete_image_<?php $image->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Delete Record Image</h3>
      </div>
      <div class="modal-body">
        <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/image/feature/<?php $image->_print('uid'); ?>/thumb" /></p>
        <p class="text-center"><?php $image->_print('notes'); ?></p>
        <p>Are you sure you want to delete this image from <?php $feature->site->_print('name'); ?>-<?php $feature->_print('record'); ?> This operation can not be reversed.</p>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/records/image_delete">
          <button type="submit" class="btn btn-danger">Delete Image</a>
          <input type="hidden" name="uid" value="<?php $image->_print('uid'); ?>">
          <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
