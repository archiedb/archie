<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Edit <?php echo scrub_out(\UI\sess::$user->site->name); ?> Feature - <?php echo scrub_out($feature->record); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_feature" method="post" action="<?php echo Config::get('web_path'); ?>/feature/update">
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How is the feature differentiated from the surrounding sediments? What are its defining characteristics?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description"><?php echo scrub_out($feature->description); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Additional Notes?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords"><?php echo scrub_out($feature->keywords); ?></textarea>
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="feature_id" value="<?php echo scrub_out($feature->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
  </div>
</div>
</form>
