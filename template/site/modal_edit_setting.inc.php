<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$title = $site->get_valid_settings();
?>
<div id="editsetting<?php echo scrub_out($key); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Edit <?php $site->_print('name'); ?> Settings</h3>
    </div>
    <div class="modal-body">
      <div class="row">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/updatesettings">
        <label class="col-md-4 control-label" for="inputKey"><?php echo $title[$key]; ?></label>
        <div class="col-md-6">
      <?php // Adjust based on what setting 
        if ($key == 'ticket') { 
          $name = 'selected_' . $site->get_setting($key);
          ${$name} = ' selected="selected"';
        ?>
          <select class="form-control" id="inputKey" name="<?php echo $key; ?>">
            <option value="88x25mm"<?php echo $selected_88x25mm; ?>>88x25mm Label</option>
            <option value="57x32mm"<?php echo $selected_57x32mm; ?>>57x32mm Label</option>
          </select>
      <?php } elseif ($key == 'catalog_offset') { ?>
          <input type="text" id="inputKey" name="<?php echo $key; ?>" value="<?php echo \UI\print_var($site->get_setting($key)); ?>" />
      <?php } else { ?>
          <textarea class="form-control" id="inputKey" name="<?php echo $key; ?>"><?php echo \UI\print_var($site->get_setting($key)); ?></textarea>
        <?php } ?>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-success">Update <?php echo $title[$key]; ?></a>
      <input type="hidden" name="site_id" value="<?php $site->_print('uid'); ?>">
      <input type="hidden" name="key" value="<?php echo scrub_out($key); ?>">
      <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
    </div>
  </div>
</div>
