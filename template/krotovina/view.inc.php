<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/krotovina/<?php $krotovina->_print('catalog_id'); ?>">View Records</a>
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/krotovina/edit/<?php $krotovina->_print('uid'); ?>">Edit Krotovina</a>
  <button type="button" class="btn btn-success" data-target="#addspatial" data-toggle="modal">Add Spatial Point</button>
</p>
<h3><?php echo $krotovina->site->name . ' ' . $krotovina->record; ?>
  <small>Entered by <?php echo $krotovina->user->username; ?> on <?php echo date("d-M-Y H:i:s",$krotovina->created); ?></small>
</h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<div class="panel panel-default">
  <div class="panel-heading">Description</div>
  <div class="panel-body"><?php $krotovina->_print('description'); ?></div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">Keywords</div>
  <div class="panel-body"><?php $krotovina->_print('keywords'); ?></div>
</div>
<h4>Krotovina Spatial Information</h4>
<table class="table table-hover">
<tr>
  <th>Station Index (RN)</th>
  <th>Northing</th>
  <th>Easting</th>
  <th>Elevation</th>
  <th>Note</th>
  <th>&nbsp;</th>
</tr>
<?php 
$spatialdata = SpatialData::get_record_data($krotovina->uid,'krotovina'); 
foreach ($spatialdata as $data) { $spatialdata = new Spatialdata($data['uid']); 
?>
<tr>
  <td><?php $spatialdata->_print('station_index'); ?></td>
  <td><?php $spatialdata->_print('northing'); ?></td>
  <td><?php $spatialdata->_print('easting'); ?></td>
  <td><?php $spatialdata->_print('elevation'); ?></td>
  <td><?php $spatialdata->_print('note'); ?></td>
  <td>
    <button type="button" data-target="#editspatial<?php $spatialdata->_print('uid'); ?>" class="btn btn-primary" data-toggle="modal">Edit</button>
    <?php include \UI\template('/krotovina/modal_edit_point'); ?>
    <button type="button" data-target="#confirmdel_<?php $spatialdata->_print('uid'); ?>" class="btn btn-danger" data-toggle="modal">Remove</button>
    <?php include \UI\template('/krotovina/modal_confirmdel_point'); ?>
  </td>
</tr>
<?php } ?>
</table>
<?php require_once \UI\template('/krotovina/modal_add_point'); ?>
