<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<fieldset><legend>Edit Site - <?php echo scrub_out($site->name); ?></legend>
<form class="form-horizontal" id="update_site" method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/update">
<div class="control-group span4<?php Error::display_class('name'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls"><input id="inputName" name="name" type="text" value="<?php echo scrub_out($site->name); ?>" /></div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('project'); ?>">
  <label class="control-label" for="inputProj">Project</label>
  <div class="controls">
    <input id="inputProj" name="project" value="<?php echo scrub_out($site->project); ?>" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">Description</label>
  <div class="controls">
  	<textarea id="inputDescription" name="description" class="textbox" rows="5"><?php echo scrub_out($site->description); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('partners'); ?>">
  <label class="control-label" for="inputPartners">Partners</label>
  <div class="controls">
  	<textarea id="inputPartners" name="partners" class="textbox" rows="5"><?php echo scrub_out($site->partners); ?></textarea>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('excavation_start'); ?>">
  <label class="control-label" for="inputExcavationStart">Excavation Start</label>
  <div class="controls">
    <input id="inputExcavationStart" name="excavation_start" type="text" placeholder="DD-MON-YY" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavation_end'); ?>">
  <label class="control-label" for="inputExcavationEnd">Excavation End</label>
  <div class="controls">
    <input id="inputExcavationEnd" name="excavation_end" type="text" placeholder="DD-MON-YY" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <input id="inputElevation" name="elevation" type="text" value="<?php echo scrub_out($site->elevation); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($site->northing); ?>" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('easting'); ?>">
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" name="easting" type="text" value="<?php echo scrub_out($site->easting); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('pi'); ?>">
  <label class="control-label" for="inputPI">Principal Investigator</label>
  <div class="controls">
  	<input id="inputPI" name="pi" type="text" value="<?php echo scrub_out($site->principal_investigator); ?>" />
  </div>
</div> 
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="site_uid" value="<?php echo scrub_out($site->uid); ?>" />
    <button class="btn btn-primary" type="submit">Update</button>
  </div>
</div>
</form>
</fieldset>
