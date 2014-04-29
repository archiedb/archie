<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Add New Site</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/create">
<div class="control-group span4<?php Error::display_class('general'); ?>">
  <label class="control-label" for="inputName">Name</label>
  <div class="controls">
    <input id="inputName" name="name" type="text" value="<?php echo scrub_out($_POST['name']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('description'); ?>">
   <label class="control-label" for="inputDescription">Description</label>
   <div class="controls">
     <input id="inputDescription" name="description" type="text" value="<?php echo scrub_out($_POST['description']); ?>" />
   </div>
</div>
<div class="control-group span4<?php Error::display_class('excavation_start'); ?>">
    <label class="control-label" for="inputExcstart">Excavation Start</label>
    <div class="controls">
      <input placeholder="DD-MONTH-YY" id="inputExcstart" name="excavation_start" type="text" value="<?php echo scrub_out($_POST['excavation_start']); ?>" />
    </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavation_end'); ?>">
    <label class="control-label" for="inputExcend">Excavation End</label>
    <div class="controls">
      <input placeholder="DD-MONTH-YY" id="inputExcend" name="excavation_end" type="text" value="<?php echo scrub_out($_POST['excavation_end']); ?>" />
    </div>
</div>
<div class="control-group span4<?php Error::display_class('pi'); ?>">
    <label class="control-label" for="inputPI">Principal Investigator</label>
    <div class="controls">
      <input id="inputPI" name="pi" type="text" value="<?php echo scrub_out($_POST['pi']); ?>" />
    </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elevation'); ?>">
    <label class="control-label" for="inputElevation">Elevation</label>
    <div class="controls">
       <input id="inputElevation" name="elevation" type="text" value="<?php echo scrub_out($_POST['elevation']); ?>" />
    </div>
</div>
<div class="control-group span4<?php Error::display_class('northing'); ?>">
    <label class="control-label" for="inputNorthing">Northing</label>
    <div class="controls">
      <input id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($_POST['northing']); ?>" />
    </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>">
    <label class="control-label" for="inputEasting">Easting</label>
    <div class="controls">
      <input id="inputEasting" name="easting" type="text" value="<?php echo scrub_out($_POST['easting']); ?>" />
    </div>
</div>
<div class="control-group span8<?php Error::display_class('partners'); ?>">
    <label class="control-label" for="inputPartners">Partners</label>
    <div class="controls">
      <textarea name="partners" class="textbox" rows="5"><?php echo scrub_out($_POST['partners']); ?></textarea>
   </div>
</div>
<div class="control-group span8"> 
   <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Add" />
   </div>
</div>
</form>
