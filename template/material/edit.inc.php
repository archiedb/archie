<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Edit <?php echo scrub_out($material->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="edit_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/material/update">
<div class="control-group span8<?php Error::display_class('name'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($material->name); ?>" />
  </div>
</div>
<div class="control-group offset1 span10">
  <h5>Associated Classifications</h5>
  <hr />
</div>
<?php 
  // Pull classifications and then foreach over them
  $classifications = Classification::get_all(); 
  // Foreach found classifications
  foreach ($classifications as $classification) { 
    $offset = ($offset == ' offset1') ? '' : ' offset1';
    $checked = '';
    if ($material->has_classification($classification->uid)) {
      $checked = 'checked="checked"';
    }
?>
<div class="control-group span4<?php echo $offset; ?>">
  <label class="checkbox">
    <input type="checkbox" name="classification[]" value="<?php echo scrub_out($classification->uid); ?>" <?php echo $checked; ?>/> <?php echo scrub_out($classification->name); ?>
  </label>
</div>
<?php } // end foreach ?>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="material_id" value="<?php echo scrub_out($material->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
  </div>
</div>
</form>
