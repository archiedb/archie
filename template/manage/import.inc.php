<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<?php Event::display('warnings'); ?>
<div class="page-header">
  <h3>Data Imports</h3>
    <em>You must indicate the type of data you are updating. If the data type selected does not match the uploaded file it will be rejected.</em>
</div>
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/manage/run_import">
  <input type="hidden" name="MAX_FILE_SIZE" value="15728640" />
<div class="row">
  <div class="form-group">
    <div class="col-md-4 col-md-offset-2">
      <input type="file" name="media" class="filestyle" data-buttonText="" data-buttonbefore="true">
    </div>
    <div class="col-md-2">
      <select class="form-control" name="type">
        <option value="xyz_station">XYZ Station</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" type="submit">Import</button>
    </div>
  </div>
</div>
</form>
<hr />
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
  <div class="col-md-2"><strong>Import Type</strong></div>
  <div class="col-md-10"><strong>Field(s)</strong></div>
</div>
<!-- XYZ Station Import -->
<div class="row">
  <div class="col-md-2">XYZ Station</div>
  <div class="col-md-10">UNIQUE('RN'),DECIMAL('Northing'),DECIMAL('Easting'),DECIMAL('Elevation'),STRING('Notes')</div>
</div>
<div class="row">
  <div class="col-md-2">Feature Info</div>
  <div class="col-md-10">INT('CATALOGID),STRING(KEYWORDS),STRING(DESCRIPTION),STRING(USERNAME),DATE(mm/dd/yy)</div>
</div>
<div class="row">
  <div class="col-md-2">Krotovina Info</div>
  <div class="col-md-10">INT('CATALOGID),STRING(KEYWORDS),STRING(DESCRIPTION),STRING(USERNAME),DATE(mm/dd/yy)</div>
</div>
<div class="row">
  <div class="col-md-2">Feature XYZ</div>
  <div class="col-md-10">INT('CATALOGID'),**INDEX('RN'),**DECIMAL('Northing'),**DECIMAL('Easting'),**DECIMAL('Elevation')</div>
</div>
<div class="row">
  <div class="col-md-2">Krotovina XYZ</div>
  <div class="col-md-10">INT('CATALOGID'),**INDEX('RN'),**DECIMAL('Northing'),**DECIMAL('Easting'),**DECIMAL('Elevation')</div>
</div>
