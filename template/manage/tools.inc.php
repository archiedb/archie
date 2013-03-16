<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
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
  <div class="span2">Thumbnails</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/thumbnail">Regenerate</a>
    </p>
  </div>
</div>
<?php Event::display('errors'); ?>
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
  <dt>INDEX</dt>
    <dd>Must match a record of specified name</dd>
</dl>
<hr />
<div class="row">
  <div class="span2"><strong>Import Type</strong></div>
  <div class="span10"><strong>Field(s)</strong></div>
</div>
<!-- XYZ Station Import -->
<div class="row">
  <div class="span2">XYZ Station</div>
  <div class="span10">INDEX('RN'),DECIMAL('Northing'),DECIMAL('Easting'),DECIMAL('Elevation'),STRING('Notes')</div>
</div>
