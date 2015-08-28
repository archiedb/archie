<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h4>New Krotovina - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_krotovina" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('description'); ?>">
    <label class="control-label" for="inputDescription">How is the krotovina differentiated from the surrounding sediments? What are its defining characteristics?</label><br />
    <div class="col-md-6">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="description" tabindex="1"><?php \UI\form_value('description'); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('keywords'); ?>">
    <label class="control-label" for="inputKeywords">Additional Notes?</label><br />
    <div class="col-md-6">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords" tabindex="2"><?php \UI\form_value('keywords'); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('station_index'); ?>">
    <label class="col-md-2 control-label" for="inputInitialRN">Station Index (RN)</label>
    <div class="col-md-2">
      <input placeholder="Station Index (RN)" class="form-control" id="inputInitialRN" name="station_index" type="text" value="<?php \UI\form_value('station_index'); ?>" tabindex="3" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" id="inputNorthing" name="northing" type="text" value="<?php \UI\form_value('northing'); ?>" tabindex="4" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" id="inputEasting" name="easting" type="text" value="<?php \UI\form_value('easting'); ?>" tabindex="5" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elevation'); ?>">
    <label class="col-md-2 control-label" for="inputElevation">Elevation</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" id="inputElevation" name="elevation" type="text" value="<?php \UI\form_value('elevation'); ?>" tabindex="6" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="col-md-2">
    	<input type="submit" class="btn btn-primary" value="Create" tabindex="7"/>
    </div>
  </div>
</div>
</form>
