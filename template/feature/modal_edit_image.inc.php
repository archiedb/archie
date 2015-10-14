<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_edit_image_<?php echo scrub_out($image->uid); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Update Image Notes</h3>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/content/update/image">
        <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/image/feature/<?php echo scrub_out($image->uid);?>/thumb" /></p>
        <p class="text-center"><input type="text" class="span4" name="description" value="<?php echo scrub_out($image->notes); ?>" /></p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Notes</a>
        <input type="hidden" name="uid" value="<?php $image->_print('uid'); ?>">
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </div>
      </form>
    </div>
  </div>
</div>
