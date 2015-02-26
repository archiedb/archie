<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$groups = Group::get_all();
?>
  <div id="del_group<?php echo $group->uid; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Remove <?php echo scrub_out($user->name); ?> from <em><?php echo scrub_out($group->name); ?></em></h3>
    </div>
    <form method="post" action="<?php echo Config::get('web_path'); ?>/users/permissions/delgroup">
    <div class="modal-body">
      Are you sure?
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-danger">Remove</a>
      <input type="hidden" name="group" value="<?php echo scrub_out($group->uid); ?>" />
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>" />
      <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
