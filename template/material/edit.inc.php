<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <p class="pull-right">
    <a href="<?php echo Config::get('web_path'); ?>/manage/material" class="btn btn-info">View Materials</a>
  </p>
  <h3>Edit <?php echo scrub_out($material->name); ?><small class="alert-danger"> Changing the name will invalidate existing records</small>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="edit_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/material/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value(array('post'=>'name','var'=>$material->name)); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
<h4>Associated Classifications</h4><hr />
</div>
<div class="row"><div class="form-group">
<?php 
  // Pull classifications and then foreach over them
  $classifications = Classification::get_all(); 
  // Foreach found classifications
  $i=0;
  foreach ($classifications as $classification) { 
    $checked = '';
    if ($material->has_classification($classification->uid)) {
      $checked = 'checked="checked"';
    }
    if (intval($i/6) == $i/6) { 
      echo '</div></div><div class="row"><div class="form-group">';
    }
    $i++;
?>
  <div class="col-md-2">
    <label class="checkbox">
      <input class="checkbox-inline" type="checkbox" name="classification[]" value="<?php echo scrub_out($classification->uid); ?>" <?php echo $checked; ?>/> <?php echo scrub_out($classification->name); ?>
    </label>
  </div>
<?php } // end foreach ?>
</div></div>
<div class="row">
  <div class="col-md-2">
    <input type="hidden" name="material_id" value="<?php echo scrub_out($material->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
    </form>
  </div>
</div>
