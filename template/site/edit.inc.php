<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h3>Edit Site - <?php echo scrub_out($site->name); ?>
  <?php echo \UI\boolean_word($site->enabled,'Enabled'); ?></h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="update_site" method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('name'); ?>">
    <label class="col-md-2 control-label" for="inputName">Name</label>
    <div class="col-md-2">
      <input class="form-control" id="inputName" name="name" type="text" value="<?php \UI\form_value(array('post'=>'name','var'=>$site->name)); ?>" />
    </div>
    </div>
    <div class="<?php Err::form_class('pi'); ?>">
    <label class="col-md-2 control-label" for="inputPI">Principal Investigator</label>
    <div class="col-md-2">
    	<input class="form-control" id="inputPI" name="pi" type="text" value="<?php \UI\form_value(array('post'=>'pi','var'=>$site->principal_investigator)); ?>" />
    </div>
    </div> 
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('partners'); ?>">
    <label class="col-md-2 control-label" for="inputPartners">Partners</label>
    <div class="col-md-2">
      <textarea class="form-control" id="inputPartners" name="partners" class="textbox" rows="5"><?php \UI\form_value(array('post'=>'partners','var'=>$site->partners)); ?></textarea>
    </div>
    </div>
    <div class="<?php Err::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input class="form-control" id="inputEasting" name="easting" type="text" value="<?php \UI\form_value(array('post'=>'easting','var'=>$site->easting)); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('elevation'); ?>">
    <label class="col-md-2 control-label" for="inputElevation">Elevation</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElevation" name="elevation" type="text" value="<?php \UI\form_value(array('post'=>'elevation','var'=>$site->elevation)); ?>" />
    </div>
    </div>
    <div class="<?php Err::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input id="inputNorthing" name="northing" type="text" value="<?php \UI\form_value(array('post'=>'northing','var'=>$site->northing)); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('excavation_end'); ?>">
    <label class="col-md-2 control-label" for="inputExcavationEnd">Excavation End</label>
    <div class="col-md-2">
      <input id="inputExcavationEnd" name="excavation_end" type="text" value="<?php \UI\form_value(array('post'=>'excavation_end','var'=>$site->excavation_end_date)); ?>" />
    </div>
    </div>
    <div class="<?php Err::form_class('excavation_start'); ?>">
    <label class="col-md-2 control-label" for="inputExcavationStart">Excavation Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputExcavationStart" name="excavation_start" type="text" value="<?php \UI\form_value(array('post'=>'excavation_start','var'=>$site->excavation_start_date)); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('description'); ?>">
    <label class="col-md-2 control-label" for="inputDescription">Description</label>
    <div class="col-md-6">
    	<textarea class="form-control" id="inputDescription" name="description" class="textbox" rows="5"><?php \UI\form_value(array('post'=>'description','var'=>$site->description)); ?></textarea>
    </div>
    </div>
      </div>
</div><div class="row">
  <div class="form-group">
  <div class="col-md-2 col-md-offset-2">
    <input type="hidden" name="site_uid" value="<?php $site->_print('uid'); ?>" />
    <button class="btn btn-primary" type="submit">Update</button>
  </div>
</div>
</form>
<?php
  include \UI\template('/site/modal_set_project');
  include \UI\template('/site/modal_set_accession');
?>
