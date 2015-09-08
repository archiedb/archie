<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<div class="page-header">
  <h3>Close Level <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>"><?php echo scrub_out($level->record); ?></a></h3>
</div>
<form class="form-horizontal" id="update_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/close">
<div class="row">
  <div class="col-md-12">
    Before closing a level, all of the below checks must be true and you must confirm that the listed items have been completed. Once 
    a level is closed, it cannot be reopened. 
  </div>
</div><div class="row">
  <label class="col-md-4 control-label">Site Manager</label>
  <div class="col-md-2">
    <?php echo \UI\boolean_word(Access::is_admin()); ?> 
  </div>
</div><div class="row">
  <label class="col-md-4 control-label">Level notes completed</label>
  <div class="col-md-2">
    <?php echo \UI\boolean_word($level->questions_answered()); ?>
  </div>
</div><div class="row">
  <label class="col-md-4 control-label">Level picture uploaded and selected</label>
  <div class="col-md-2">
    <?php echo \UI\boolean_word($level->has_photo()); ?>
  </div>
</div>
  <h4>Level Checklist</h4><hr />
<div class="row">
<div class="form-group">
  <div class="col-md-6 col-md-offset-4">
    <input type="checkbox" name="kroto_sample" value="1" />
    Krotovina sediments sampled
  </div>
</div>
<div class="form-group">
  <div class="col-md-6 col-md-offset-4">
  <input type="checkbox" name="kroto_bag" value="1" />
  Krotovina screenings bagged
  </div>
</div>
<div class="form-group">
  <div class="col-md-6 col-md-offset-4">
  <input type="checkbox" name="level_photo" value="1" />
  Level photograph taken and uploaded
  </div>
</div>
<div class="form-group">
  <div class="col-md-6 col-md-offset-4">
  <input type="checkbox" name="notes_done" value="1" />
  Field notes completed
  </div>
</div>
<div class="form-group">
  <div class="col-md-6 col-md-offset-4">
  <input type="checkbox" name="connect" value="1" />
  Ending elevations taken
  </div>
</div>
<div class="form-group">
  <div class="col-md-2 col-md-offset-4">
    <input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
    <button class="btn btn-primary" type="submit">Close</button>
  </div>
</form>
</div>
