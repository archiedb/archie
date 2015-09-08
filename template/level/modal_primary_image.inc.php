<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_primary_image_<?php echo scrub_out($image->uid); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Primary Level Image</h3>
      </div>
      <div class="modal-body">
        <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/image/level/<?php $image->_print('uid'); ?>/thumb" /></p>
        <p class="text-center"><?php $image->_print('notes'); ?></p>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/level/image_primary">
          <button type="submit" class="btn btn-primary">Make Level Image</a>
          <input type="hidden" name="uid" value="<?php $level->_print('uid'); ?>">
          <input type="hidden" name="image" value="<?php $image->_print('uid'); ?>">
          <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
