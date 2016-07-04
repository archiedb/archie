<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h4>
    Add Classification
    <small>Classification Names must be unique, and all new classifications will be added in a disabled state</small>
  </h4>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_classification" method="post" action="<?php echo Config::get('web_path'); ?>/manage/classification/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value('name'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('description'); ?>">
    <label class="col-md-2 control-label" for="inputDescription">Description</label>
    <div class="col-md-2">
      <input class="form-control" id="inputDescription" name="description" type="text" value="<?php \UI\form_value('description'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="col-md-2 col-md-offset-2">
  	  <input type="submit" class="btn btn-primary" value="Add" />
    </div>
  </div>
</div>
</form>
