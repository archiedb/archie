<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <?php if (Access::has('record','delete')) { ?>
  <a href="#confirmdel_<?php echo scrub_out($record->uid); ?>" role="button" data-toggle="modal" class="btn btn-danger">Delete</a>
  <?php } ?>
  <a href="<?php echo Config::get('web_path'); ?>/records/edit/<?php echo scrub_out($record->uid); ?>" class="btn">Edit Record</a>
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/records/print/<?php echo scrub_out($record->uid); ?>/ticket" class="btn btn-success">Print Ticket</a>
</p>
  <?php if (Access::has('record','delete')) { include \UI\template('/records/modal_delete_record');  } ?>
<h3><?php echo $record->site->name . '-' . $record->catalog_id; ?>
  <small>Entered by <?php echo $record->user->username; ?> on <?php echo date("d-M-Y H:i:s",$record->created); ?></small>
</h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Unit</th><td><?php echo scrub_out($record->level->unit); ?></em></td>
  <th>Catalog ID</th><td><?php echo scrub_out($record->site->name . '-' . $record->catalog_id); ?></td>
</tr>
<tr>
  <th>Level</th><td><?php echo \UI\record_link($record->level->uid,'level',$record->level->catalog_id); ?></td>
  <th><abbr title="Lithostratoigraphic Unit">L. U.</abbr></th><td><?php echo scrub_out($record->lsg_unit->name); ?></td>
</tr>
<tr>
  <th>Feature</th><td><?php echo \UI\record_link($record->feature->uid,'feature',$record->feature->record); ?></td>
  <th>Krotovina</th><td><?php echo \UI\record_link($record->krotovina->uid,'krotovina',$record->krotovina->record); ?></td>
</tr>
<tr>
  <th>Quad</th><td><?php echo scrub_out($record->level->quad->name); ?></td>
  <th>RN</th><td><?php echo scrub_out($record->station_index); ?></td>
</tr>
<tr>
  <th>Weight</th><td><?php echo scrub_out($record->weight); ?> grams</td>
  <th>Thickness</th><td><?php echo scrub_out($record->thickness); ?> mm</td>
</tr>
<tr>
  <th>Length</th><td><?php echo scrub_out($record->height); ?> mm</td>
  <th>Width</th><td><?php echo scrub_out($record->width); ?> mm</td>
<tr>
  <th>Material</th><td><?php echo scrub_out($record->material->name); ?></td>
  <th>Classification</th><td><?php echo scrub_out($record->classification->name); ?></td>
</tr>
<tr>
  <th>Matrix XRF #</th><td><?php echo scrub_out($record->xrf_matrix_index); ?></td>
  <th>Artifact XRF #</th><td><?php echo scrub_out($record->xrf_artifact_index); ?></td>
</tr>
<tr>
  <th>Northing</th><td><?php echo scrub_out($record->northing); ?></td>
  <th>Easting</th><td><?php echo scrub_out($record->easting); ?></td>
<tr>
  <th>Elevation</th><td><?php echo scrub_out($record->elevation); ?></td>
  <th>Quanity</th><td><?php echo scrub_out($record->quanity); ?></td>
</tr>
<tr>
  <th>Accession</th><td><?php echo $record->_print('accession'); ?></td>
  <th>Notes</th><td><?php echo scrub_out($record->notes); ?></td>
</tr>
  
</table>
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Pictures</a></li>
  <li><a href="#3dmodel" data-toggle="tab">3D Models</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/records/images'); ?>
  </div>
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/records/3dmodel'); ?>
  </div>
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/records/media'); ?>
  </div>
</div>
