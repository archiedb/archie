<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_edit_3dmodel_<?php echo scrub_out($model->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/records/3dmodel_edit">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Update 3D Model Notes</h3>
  </div>
  <div class="modal-body">
    <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/3dmodel/<?php echo scrub_out($model->uid);?>" /></p>
    <p class="text-center"><input type="text" class="span4" name="description" value="<?php echo scrub_out($image->notes); ?>" /></p>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">Update Notes</a>
    <input type="hidden" name="uid" value="<?php echo scrub_out($model->uid); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
