<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h3>Add New Site</h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_material" method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('name'); ?>">
    <label class="control-label col-md-2" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value('name'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('pi'); ?>">
    <label class="control-label col-md-2" for="inputPI">Principal Investigator</label>
    <div class="col-md-2">
      <input class="form-control" id="inputPI" name="pi" type="text" value="<?php \UI\form_value('pi'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('description'); ?>">
    <label class="control-label col-md-2" for="inputDescription">Description</label>
    <div class="col-md-2">
      <textarea class="form-control" id="inputDescription" name="description" class="textbox" rows="5"><?php \UI\form_value('description'); ?></textarea>
   </div>
   </div>
    <div class="<?php Error::form_class('partners'); ?>">
    <label class="control-label col-md-2" for="inputPartners">Partners</label>
    <div class="col-md-2">
      <textarea class="form-control" id="inputPartners" name="partners" class="textbox" rows="5"><?php \UI\form_value('partners'); ?></textarea>
   </div>
   </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elevation'); ?>">
    <label class="control-label col-md-2" for="inputElevation">Elevation</label>
    <div class="col-md-2">
       <input class="form-control" id="inputElevation" name="elevation" type="text" value="<?php \UI\form_value('elevation'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="control-label col-md-2" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input class="form-control" id="inputNorthing" name="northing" type="text" value="<?php \UI\form_value('northing'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="control-label col-md-2" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input class="form-control" id="inputEasting" name="easting" type="text" value="<?php \UI\form_value('easting'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('excavation_start'); ?>">
    <label class="control-label col-md-2" for="inputExcavationStart">Excavation Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputExcavationStart" name="excavation_start" type="text" value="<?php \UI\form_value('excavation_start'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('excavation_end'); ?>">
    <label class="control-label col-md-2" for="inputExcavationEnd">Excavation End</label>
    <div class="col-md-2">
      <input class="form-control" id="inputExcavationEnd" name="excavation_end" type="text" value="<?php \UI\form_value('excavation_end'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="col-md-2 col-md-offset-2">
    	<input type="submit" class="btn btn-primary" value="Create" />
    </div>
  </div>
</div>
</form>
