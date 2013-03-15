<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
  <div id="confirm_enable_user_<?php echo scrub_out($user->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Confirm Enable User</h3>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to enable <?php echo scrub_out($user->username); ?>?</p>
    </div>
    <div class="modal-footer">
    <form method="post" action="<?php echo Config::get('web_path'); ?>/users/enable">
      <button type="submit" class="btn btn-success">Enable</a>
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>" />
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
