<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/feature/edit/<?php echo scrub_out($feature->uid); ?>" class="btn">Edit Feature</a>
</p>
<h3><?php echo $feature->site->name . ' F-' . $feature->catalog_id; ?>
  <small>Entered by <?php echo $feature->user->username; ?> on <?php echo date("d-M-Y H:i:s",$feature->created); ?></small>
</h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Keywords</th><td><?php echo scrub_out($feature->keywords); ?></em></td>
  <th>User</th><td><?php echo scrub_out($feature->user->name); ?></td>
</tr>
<tr>
  <th>Description</th><td colspan="3"><?php echo scrub_out($feature->description); ?></td>
</tr>
</table>
<h5>Feature Spatial Information</h5>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>RN</th>
  <th>Northing</th>
  <th>Easting</th>
  <th>Elevation</th>
  <th>Note</th>
</tr>
<?php 
$spatialdata = SpatialData::get_record_data($feature->uid,'feature'); 
foreach ($spatialdata as $data) { $spatialdata = new Spatialdata($data['uid']); 
?>
<tr>
  <td><?php echo scrub_out($spatialdata->station_index); ?></td>
  <td><?php echo scrub_out($spatialdata->northing); ?></td>
  <td><?php echo scrub_out($spatialdata->easting); ?></td>
  <td><?php echo scrub_out($spatialdata->elevation); ?></td>
  <td><?php echo scrub_out($spatialdata->note); ?></td>
</tr>
<?php } ?>
</table>