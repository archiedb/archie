<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Add Material</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/material/create">
<div class="control-group span8">
  <p class="text-center">Material Names must be unique, and all new materials will be added in a disabled state</p>
</div>
<div class="control-group span8<?php Error::display_class('general'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($_POST['name']); ?>" />
  	<input type="submit" class="btn btn-primary" value="Add" />
  </div>
</div>
</form>
