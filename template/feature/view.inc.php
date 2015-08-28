<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/feature/<?php echo scrub_out($feature->catalog_id); ?>">View Records</a>
  <a href="<?php echo Config::get('web_path'); ?>/feature/edit/<?php echo scrub_out($feature->uid); ?>" class="btn btn-primary">Edit Feature</a>
  <a class="btn btn-success" href="#addspatial" role="button" data-toggle="modal">Add Spatial Point</a>
</p>
<h3><?php echo $feature->site->name . ' F-' . $feature->catalog_id; ?>
  <small>Entered by <?php echo $feature->user->username; ?> on <?php echo date("d-M-Y H:i:s",$feature->created); ?></small>
</h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Description</th><td colspan="3"><?php $feature->_print('description'); ?></td>
  <th>Other Notes?</th><td><?php $feature->_print('keywords'); ?></em></td>
</tr>
</table>
<h4>Feature Spatial Information</h4>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Station Index (RN)</th>
  <th>Northing</th>
  <th>Easting</th>
  <th>Elevation</th>
  <th>Note</th>
  <th>&nbsp;</th>
</tr>
<?php 
$spatialdata = SpatialData::get_record_data($feature->uid,'feature'); 
foreach ($spatialdata as $data) { $spatialdata = new Spatialdata($data['uid']); 
?>
<tr>
  <td><?php $spatialdata->_print('station_index'); ?></td>
  <td><?php $spatialdata->_print('northing'); ?></td>
  <td><?php $spatialdata->_print('easting'); ?></td>
  <td><?php $spatialdata->_print('elevation'); ?></td>
  <td><?php $spatialdata->_print('note'); ?></td>
  <td>
    <?php if (Access::has('feature','edit')) { ?>
    <button type="button" data-target="#editspatial<?php $spatialdata->_print('uid'); ?>" class="btn btn-primary" data-toggle="modal">Edit</button>
    <button type="button" data-target="#confirmdel_<?php $spatialdata->_print('uid'); ?>" class="btn btn-danger" data-toggle="modal">Delete</button>
    <?php include \UI\template('/feature/modal_confirmdel_point'); ?>
    <?php include \UI\template('/feature/modal_edit_point'); ?>
    <?php } ?>
  </td>
</tr>
<?php } ?>
</table>
<?php require_once \UI\template('/feature/modal_add_point'); ?>

