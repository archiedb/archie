<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="delete_group_<?php echo $group->uid; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm deletion of <em><?php $group->_print('name'); ?></em> Group</h3>
      </div>
      <div class="modal-body">
      <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/group/delete">
        Are you sure you want to permenantly delete <code><?php $group->_print('name'); ?></code>. This group will also be removed
        from any users it's been assigned to. This can leave users without any effective permissions. 
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Delete</a>
        <input type="hidden" name="uid" value="<?php $group->_print('uid'); ?>" />
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
