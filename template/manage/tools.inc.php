<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<?php Event::display('warnings'); ?>
<div class="page-header">
  <h3>Image Maintenance</h3>
  <em>These tasks are scheduled and run every 5 min. Some of these tasks may take a long time to complete. You will be e-mailed when it has finished</em>
</div>
<div class="row">
  <div class="span2"><strong>Task</strong></div>
  <div class="span3"><strong>Status</strong></div>
</div>
<div class="row">
  <?php $cron = new Cron('qrcode'); ?>
  <div class="span2">QRCodes</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/qrcode">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('thumb'); ?>
  <div class="span2">Image Thumbs</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/thumbnail">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('3dmodel_thumb'); ?>
  <div class="span2">3D Model Thumbs</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/3dmodel_thumb">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('scatterplots'); ?>
  <div class="span2">Level Report Plots</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/scatterplots">Regenerate</a>
    </p>
  </div>
</div>

<div class="page-header">
  <h3>Data Imports</h3>
    <em>You must indicate the type of data you are updating. If the data type selected does not match the uploaded file it will be rejected.</em>
</div>
<div class="row">
<div class="span4">
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/manage/import">
  <input type="hidden" name="MAX_FILE_SIZE" value="15728640" />
<div class="fileupload fileupload-new" data-provides="fileupload">
  <div class="input-append">
    <div class="uneditable-input span3">
      <i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span>
    </div>
    <span class="btn btn-file"><span class="fileupload-new">Select</span>
    <span class="fileupload-exists">Change</span><input name="import" type="file" /></span>
    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
  </div>
</div>
</div>
<div class="span3 offset1">
  <select name="type">
      <option value="xyz_station">XYZ Station</option>
  </select>
</div>
<div class="span1">
  <button class="btn btn-primary" type="submit">Import</button>
</div>
</div>
</form>
<p>Expected import file formats are listed below:</p>
<dl class="dl-horizontal">
  <dt>INT</dt>
    <dd>0-9</dd>
  <dt>DECIMAL</dt>
    <dd>0-9, may contain decimal</dd>
  <dt>STRING</dt>
    <dd>Any character</dd>
  <dt>ENUM</dt>
    <dd>Pre-defined list of values</dd>
  <dt>INDEX</dt>
    <dd>Must match a record of specified name</dd>
  <dt>DATE</dt>
    <dd>Allowed formats mm/dd/yy dd-mm-yy yy.mm.dd pay special attention to seperating character</dd>
  <dt>UNIQUE</dt>
    <dd>Must be unique to the site</dd>
  <dt>**</dt>
    <dd>Denotes an optional field - see included documentation for more information on importing methods</dd>
</dl>
<hr />
<div class="row">
  <div class="span2"><strong>Import Type</strong></div>
  <div class="span10"><strong>Field(s)</strong></div>
</div>
<!-- XYZ Station Import -->
<div class="row">
  <div class="span2">XYZ Station</div>
  <div class="span10">UNIQUE('RN'),DECIMAL('Northing'),DECIMAL('Easting'),DECIMAL('Elevation'),STRING('Notes')</div>
</div>
<div class="row">
  <div class="span2">Feature Info</div>
  <div class="span10">INT('CATALOGID),STRING(KEYWORDS),STRING(DESCRIPTION),STRING(USERNAME),DATE(mm/dd/yy)</div>
</div>
<div class="row">
  <div class="span2">Krotovina Info</div>
  <div class="span10">INT('CATALOGID),STRING(KEYWORDS),STRING(DESCRIPTION),STRING(USERNAME),DATE(mm/dd/yy)</div>
</div>
<div class="row">
  <div class="span2">Feature XYZ</div>
  <div class="span10">INT('CATALOGID'),**INDEX('RN'),**DECIMAL('Northing'),**DECIMAL('Easting'),**DECIMAL('Elevation')</div>
</div>
<div class="row">
  <div class="span2">Krotovina XYZ</div>
  <div class="span10">INT('CATALOGID'),**INDEX('RN'),**DECIMAL('Northing'),**DECIMAL('Easting'),**DECIMAL('Elevation')</div>
</div>
