<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_open_level_<?php $level->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Re-Open Level</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to re-open this level, this may invalidate any previously printed Level reports. Please re-generate report after level is closed again.</p>
      </div>
      <div class="modal-footer">
      <form method="post" action="<?php echo Config::get('web_path'); ?>/level/reopen_level">
        <button type="submit" class="btn btn-danger">Re-Open Level</a>
        <input type="hidden" name="uid" value="<?php $level->_print('uid'); ?>">
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
