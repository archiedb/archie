<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Edit <?php echo scrub_out(\UI\sess::$user->site->name); ?> Krotovina - <?php echo scrub_out($krotovina->record); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_krotovina" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/update">
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How is it defined?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description"><?php echo scrub_out($krotovina->description); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Keywords</label>
  <div class="controls">
    <input id="inputKeywords" name="keywords" type="text" value="<?php echo scrub_out($krotovina->keywords); ?>" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="krotovina_id" value="<?php echo scrub_out($krotovina->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
  </div>
</div>
</form>
