<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
  <div id="add_role" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Add Role to <em><?php echo scrub_out($group->name); ?></em> Group</h3>
    </div>
    <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/group/addrole">
    <div class="modal-body">
      <?php $roles = Role::get_all(); ?>
      <select name="role">
        <? foreach ($roles as $role) { ?>
        <option value="<?php echo $role->uid; ?>"><?php echo $role->description; ?></option>
        <?php } ?>
      </select>
      <?php $actions = Action::get_all(); ?>
      <select name="action">
        <?php foreach ($actions as $action) { ?>
        <option value="<?php echo $action->uid; ?>"><?php echo $action->description; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-success">Add</a>
      <input type="hidden" name="uid" value="<?php echo scrub_out($group->uid); ?>" />
      <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
