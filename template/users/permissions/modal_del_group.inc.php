<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$groups = Group::get_all();
?>
<div id="del_group<?php $group->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Remove <?php $user->_print('name'); ?> from <em><?php $group->_print('name'); ?></em></h3>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/users/permissions/delgroup">
        Are you sure you want to remove the <code><?php $group->_print('name'); ?></code> group from the <code><?php $user->_print('name'); ?></code> user? 
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Remove</a>
        <input type="hidden" name="group" value="<?php $group->_print('uid'); ?>" />
        <input type="hidden" name="uid" value="<?php $user->_print('uid'); ?>" />
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
       </form>
      </div>
    </div>
  </div>
</div>
