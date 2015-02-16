<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Group</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_group" method="post" action="<?php echo Config::get('web_path'); ?>/manage/group/create">
<div class="control-group span8 <?php Error::display_class('name'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($_POST['name']); ?>" tabindex="3" />
  </div>
</div>
<div class="control-group span8 <?php Error::display_class('desc'); ?>">
  <label class="control-label" for="inputDesc">Description</label>
  <div class="controls">
    <input id="inputDesc" name="description" type="text" value="<?php echo scrub_out($_POST['description']); ?>" tabindex="4" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" tabindex="5"/>
  </div>
</div>
</form>
