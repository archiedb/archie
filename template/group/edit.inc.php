<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h3>Edit <?php $group->_print('name'); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="edit_group" method="post" action="<?php echo Config::get('web_path'); ?>/manage/group/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value(array('post'=>'description','var'=>$group->name)); ?>" tabindex="3" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('description'); ?>">
    <label class="col-md-2 control-label" for="inputDesc">Description</label>
    <div class="col-md-2">
      <input class="form-control" id="inputDesc" name="description" type="text" value="<?php \UI\form_value(array('post'=>'description','var'=>$group->description)); ?>" tabindex="4" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
  <div class="col-md-2 col-md-offset-2">
    <input type="hidden" name="group" value="<?php echo $group->uid; ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" tabindex="5"/>
    </form>
  </div>
  </div>
</div>
