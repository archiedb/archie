<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_delete_media_<?php $media->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Delete Media Item</h3>
      </div>
      <div class="modal-body">
        <p class="text-center"><?php $media->_print('notes'); ?></p>
        <p>Are you sure you want to delete <code>(<?php echo scrub_out(basename($media->filename)); ?>)</code> from <?php echo $record->site->name . '-' . $record->catalog_id; ?>? This operation can not be reversed.</p>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/records/media_delete">
          <button type="submit" class="btn btn-danger">Delete Media</a>
          <input type="hidden" name="uid" value="<?php echo scrub_out($media->uid); ?>">
          <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
