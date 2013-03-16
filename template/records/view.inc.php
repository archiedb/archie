<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/records/edit/<?php echo scrub_out($record->uid); ?>" class="btn">Edit Record</a>
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/records/print/<?php echo scrub_out($record->uid); ?>/ticket" class="btn btn-success">Print Ticket</a>
</p>
<?php Event::display(); ?>
<h3><?php echo $record->site . '-' . $record->catalog_id; ?>
  <small>Entered by <?php echo $record->user->username; ?> on <?php echo date("d-M-Y H:i:s",$record->created); ?></small>
</h3>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>UNIT</th><td><?php echo scrub_out($record->unit); ?></em></td>
  <th>CATALOG ID</th><td><?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></td>
</tr>
<tr>
  <th>LEVEL</th><td><?php echo scrub_out($record->level); ?></td>
  <th>L. U.</th><td><?php echo scrub_out($record->lsg_unit->name); ?></td>
</tr>
<tr>
  <th>QUANTITY</th><td><?php echo scrub_out($record->quanity); ?></td>
  <th>RN</th><td><?php echo scrub_out($record->station_index); ?></td>
</tr>
<tr>
  <th>QUAD</th><td><?php echo scrub_out($record->quad->name); ?></td>
  <th>FEATURE</th><td><?php echo scrub_out($record->feature); ?></td>
</tr>
<tr>
  <th>WEIGHT</th><td><?php echo scrub_out($record->weight); ?> grams</td>
  <th>THICKNESS</th><td><?php echo scrub_out($record->thickness); ?> mm</td>
</tr>
<tr>
  <th>LENGTH</th><td><?php echo scrub_out($record->height); ?> mm</td>
  <th>WIDTH</th><td><?php echo scrub_out($record->width); ?> mm</td>
<tr>
  <th>MATERIAL</th><td><?php echo scrub_out($record->material->name); ?></td>
  <th>CLASSIFICATION</th><td><?php echo scrub_out($record->classification->name); ?></td>
</tr>
<tr>
  <th>MATRIX XRF #</th><td><?php echo scrub_out($record->xrf_matrix_index); ?></td>
  <th>ARTIFACT XRF #</th><td><?php echo scrub_out($record->xrf_artifact_index); ?></td>
</tr>
<tr>
  <th>NORTHING</th><td><?php echo scrub_out($record->northing); ?></td>
  <th>EASTING</th><td><?php echo scrub_out($record->easting); ?></td>
<tr>
  <th>ELEVATION</th><td><?php echo scrub_out($record->elevation); ?></td>
  <th>NOTES</th><td><?php echo scrub_out($record->notes); ?></td>
</tr>
</table>
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Pictures</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/records/images'); ?>
  </div>
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/records/media'); ?>
  </div>
</div>
