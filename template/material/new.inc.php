<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <p class="pull-right">
    <a href="<?php echo Config::get('web_path'); ?>/manage/material" class="btn btn-info">View Materials</a>
  </p>

  <h3>
    Add Material<small> Material Names must be unique, and all new materials will be added in a disabled state</small>
  </h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/material/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value('name'); ?>" />
    </div>
    </div>
    <div class="col-md-2">
    	<input type="submit" class="btn btn-primary" value="Add" />
      </form>
    </div>
    </div>
  </div>
</div>
