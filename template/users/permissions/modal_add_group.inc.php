<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$groups = Group::get_all();
?>
<div id="add_group" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Add <em><?php echo scrub_out($user->name); ?></em> to Group</h3>
      </div>
      <form method="post" action="<?php echo Config::get('web_path'); ?>/users/permissions/addgroup">
      <div class="modal-body">
        <select class="form-control" name="group">
          <?php foreach ($groups as $group) { ?>
          <option value="<?php echo $group->uid; ?>"><?php echo scrub_out($group->name); ?> (<?php echo scrub_out($group->description); ?>)</option>
          <?php } ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Add</a>
        <input type="hidden" name="uid" value="<?php $user->_print('uid'); ?>" />
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
