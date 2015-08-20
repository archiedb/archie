<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="editsetting<?php echo scrub_out($key); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/updatesettings">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Edit <?php $site->_print('name'); ?> Settings</h3>
  </div>
  <div class="modal-body">
    <div class="control-group span4">
      <label class="control-label" for="inputKey"><?php echo ucfirst($key); ?></label>
      <div class="controls">
    <?php // Adjust based on what setting 
      if ($key == 'ticket') { 
        $name = 'selected_' . $site->$key;
        ${$name} = ' selected="selected"';
      ?>
        <select id="inputKey" name="<?php echo $key; ?>">
          <option value="88x25mm"<?php echo $selected_88x25mm; ?>>88x25mm Label</option>
          <option value="57x32mm"<?php echo $selected_57x32mm; ?>>57x32mm Label</option>
        </select>
    <?php } else { ?>
        <textarea id="inputKey" name="<?php echo $key; ?>"><?php echo \UI\print_var($site->$key); ?></textarea>
    <?php } ?>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success">Update <?php echo ucfirst($key); ?></a>
    <input type="hidden" name="site_id" value="<?php $site->_print('uid'); ?>">
    <input type="hidden" name="key" value="<?php echo scrub_out($key); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
