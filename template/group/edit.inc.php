<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Edit <em><?php echo scrub_out($group->name); ?></em></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="edit_group" method="post" action="<?php echo Config::get('web_path'); ?>/manage/group/update">
<div class="control-group span8 <?php Error::display_class('name'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($group->name); ?>" tabindex="3" />
  </div>
</div>
<div class="control-group span8 <?php Error::display_class('desc'); ?>">
  <label class="control-label" for="inputDesc">Description</label>
  <div class="controls">
    <input id="inputDesc" name="description" type="text" value="<?php echo scrub_out($group->description); ?>" tabindex="4" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="group" value="<?php echo $group->uid; ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" tabindex="5"/>
  </div>
</div>
</form>
