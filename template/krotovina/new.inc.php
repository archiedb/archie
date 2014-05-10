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
  <label class="control-label" for="inputDescription">How is it defined?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description"><?php echo scrub_out($_POST['description']); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Keywords</label>
  <div class="controls">
    <input id="inputKeywords" name="keywords" type="text" value="<?php echo scrub_out($_POST['keywords']); ?>" />
  </div>
</div>
<div class="control-group offset1 span10">
  <h5>Initial RN / Northing, Easting and Elevation</h5>
  <hr />
  <i class="muted"><small>Do not enter Northing, Easting and Elevation if using RNs from a Total Station</small></i>
</div>
<div class="control-group span4<?php Error::display_class('initial_rn'); ?>">
  <label class="control-label" for="inputInitialRN">Initial RN</label>
  <div class="controls">
    <input id="inputInitialRN" name="initial_rn" type="text" value="<?php echo scrub_out($_POST['initial_rn']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($_POST['northing']); ?>" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('easting'); ?>"> 
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" name="easting" type="text" value="<?php echo scrub_out($_POST['easting']); ?>" />
  </div>
</div>
<div class="control-grouP span4 offset1<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <input id="inputElevation" name="elevation" type="text" value="<?php echo scrub_out($_POST['elevation']); ?>" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
