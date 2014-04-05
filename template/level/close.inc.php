<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<fieldset><legend>Close Level - <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>"><?php echo scrub_out($level->site->name . '-' . $level->record . '-' . $level->unit . '-' . $level->quad->name); ?></a></legend>
<form class="form-horizontal" id="update_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/close">
<div class="control-group span10">
Before closing a level, all of the below checks must be true and you must confirm that the listed items have been completed. Once 
a level is closed, it cannot be reopened. 
</div>
<div class="control-group span4 offset3">
  <label class="control-label">Excavator of Level</label>
  <div class="controls">
    <?php echo \UI\boolean_word($level->is_excavator(\UI\sess::$user->uid)); ?> 
  </div>
</div>
<div class="control-group span4 offset3">
  <label class="control-label">Questions answered</label>
  <div class="controls">
    <?php echo \UI\boolean_word($level->questions_answered()); ?>
  </div>
</div>
<div class="control-group span4 offset3">
  <label class="control-label">Level picture uploaded and selected</label>
  <div class="controls">
    <?php echo \UI\boolean_word($level->has_photo()); ?>
  </div>
</div>
<div class="control-group span10">
  <h4>Level Checklist</h4><hr />
</div>
<div class="control-group span4 offset4">
  <input type="checkbox" name="kroto_sample" value="1" />
  Krotovina sediments sampled
</div>
<div class="control-group span4 offset4">
  <input type="checkbox" name="kroto_bag" value="1" />
  Krotovina screenings bagged
</div>
<div class="control-group span4 offset4">
  <input type="checkbox" name="level_photo" value="1" />
  Level photograph taken and uploaded
</div>
<div class="control-group span4 offset4">
  <input type="checkbox" name="notes_done" value="1" />
  Field notes completed
</div>
<div class="control-group span4 offset4">
  <input type="checkbox" name="connect" value="1" />
  Kinect Scan
</div>
<div class="control-group span4 offset3">
  <div class="controls">
    <input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
    <button class="btn btn-primary" type="submit">Close</button>
  </div>
</form>
</fieldset>

