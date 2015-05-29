<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="set_project_<?php echo $site->_print('uid'); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/setproject">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Set <?php echo $site->_print('name'); ?> Project</h3>
  </div>
  <div class="modal-body">
    <em>Set the current project for <?php echo $site->_print('name'); ?>. This will overwrite any existing project set on this site. 
    Site projects start and end dates will be kept for historical reference.</em>
    <hr />
    <p>
    <strong>Project:</strong> <input type="text" name="project" />
    </p>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">Set Project</a>
    <input type="hidden" name="uid" value="<?php echo $site->_print('uid'); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
