<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_delete_3model_<?php echo scrub_out($model->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Confirm Delete 3D Model</h3>
  </div>
  <div class="modal-body">
    <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/3model/<?php echo scrub_out($model->uid);?>" /></p>
    <p class="text-center"><?php echo scrub_out($model->notes); ?></p>
    <p>Are you sure you want to delete this 3D Model from <?php echo $record->site . '-' . $record->catalog_id; ?>? This operation can not be reversed.</p>
  </div>
  <div class="modal-footer">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/records/image_delete">
    <button type="submit" class="btn btn-danger">Delete 3D Model</a>
    <input type="hidden" name="uid" value="<?php echo scrub_out($model->uid); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </form>
  </div>
</div>
