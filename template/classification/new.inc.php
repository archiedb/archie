<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Add Classification</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_classification" method="post" action="<?php echo Config::get('web_path'); ?>/manage/classification/create">
<div class="control-group span8">
  <p class="text-center">Classification Names must be unique, and all new classifications will be added in a disabled state</p>
</div>
<div class="control-group span8<?php Error::display_class('general'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($_POST['name']); ?>" />
  </div>
</div>
<div class="control-group span8<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">Description</label>
  <div class="controls">
    <input id="inputDescription" name="description" type="text" value="<?php echo scrub_out($_POST['description']); ?>" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Add" />
  </div>
</div>
</form>
