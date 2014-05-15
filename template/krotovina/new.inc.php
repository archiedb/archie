<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Krotovina - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_krotovina" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/create">
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How is the krotovina differentiated from the surrounding sediments? What are its defining characteristics?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description" tabindex="1"><?php echo scrub_out($_POST['description']); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('initial_rn'); ?>">
  <label class="control-label" for="inputInitialRN">Initial RN</label>
  <div class="controls">
    <input id="inputInitialRN" name="initial_rn" type="text" value="<?php echo scrub_out($_POST['initial_rn']); ?>" tabindex="3" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($_POST['northing']); ?>" tabindex="4" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>"> 
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" name="easting" type="text" value="<?php echo scrub_out($_POST['easting']); ?>" tabindex="5" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Additional Notes?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords" tabindex="2"><?php echo scrub_out($_POST['keywords']); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <input id="inputElevation" name="elevation" type="text" value="<?php echo scrub_out($_POST['elevation']); ?>" tabindex="6" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" tabindex="7"/>
  </div>
</div>
</form>
