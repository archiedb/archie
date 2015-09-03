<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_disable_user_<?php $user->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Disable User</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to disable the user <code><?php $user->_print('name'); ?>(<?php $user->_print('username'); ?>)</code>. They will be unable to login.</p>
      </div>
      <div class="modal-footer">
      <form method="post" action="<?php echo Config::get('web_path'); ?>/users/disable">
        <button type="submit" class="btn btn-danger">Disable</a>
        <input type="hidden" name="uid" value="<?php $user->_print('uid'); ?>" />
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
