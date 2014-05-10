<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/krotovina/edit/<?php echo scrub_out($krotovina->uid); ?>" class="btn">Edit Krotovina</a>
</p>
<h3><?php echo $krotovina->site->name . ' ' . $krotovina->record; ?>
  <small>Entered by <?php echo $krotovina->user->username; ?> on <?php echo date("d-M-Y H:i:s",$krotovina->created); ?></small>
</h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Keywords</th><td><?php echo scrub_out($krotovina->keywords); ?></em></td>
  <th>User</th><td><?php echo scrub_out($krotovina->user->name); ?></td>
</tr>
<tr>
  <th>Description</th><td colspan="3"><?php echo scrub_out($krotovina->description); ?></td>
</tr>
</table>
<h5>Krotovina Spatial Information</h5>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>RN</th>
  <th>Northing</th>
  <th>Easting</th>
  <th>Elevation</th>
  <th>Note</th>
</tr>
<?php 
$spatialdata = SpatialData::get_record_data($krotovina->uid,'krotovina'); 
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
