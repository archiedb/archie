<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/records/print/<?php echo scrub_out($record->uid); ?>/ticket" class="btn btn-success">Print Ticket</a>
  <a href="<?php echo Config::get('web_path'); ?>/records/edit/<?php echo scrub_out($record->uid); ?>" class="btn btn-primary">Edit Record</a>
  <?php if (Access::has('record','delete')) { ?>
  <button type="button" data-target="#confirmdel_<?php echo scrub_out($record->uid); ?>" data-toggle="modal" class="btn btn-danger">Delete</button>
  <?php } ?>
  <?php if (Access::has('record','delete')) { include \UI\template('/records/modal_delete_record');  } ?>
</p>
<h3><?php echo $record->site->name . '-' . $record->catalog_id; ?>
  <small>Entered by <?php echo $record->user->username; ?> on <?php echo date("d-M-Y H:i:s",$record->created); ?></small>
</h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>

<table class="table table-hover table-white">
<tr>
  <th>Unit</th><td><?php echo scrub_out($record->level->unit->name); ?></em></td>
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
  <th>Station Index (RN)</th><td><?php echo scrub_out($record->station_index); ?></td>
</tr>
<tr>
  <th>Weight</th><td><?php echo empty($record->weight) ? '' : $record->weight . 'g'; ?></td>
  <th>Thickness</th><td><?php echo empty($record->thickness) ? '' : $record->thickness . 'mm'; ?></td>
</tr>
<tr>
  <th>Length</th><td><?php echo empty($record->height) ? '' : $record->height . 'mm'; ?></td>
  <th>Width</th><td><?php echo empty($record->width) ? '' : $record->width . 'mm'; ?></td>
<tr>
  <th>Material</th><td><?php echo \UI\search_link('material',$record->material->name); ?></td>
  <th>Classification</th><td><?php echo \UI\search_link('classification',$record->classification->name); ?></td>
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
  <th>Quantity</th><td><?php echo scrub_out($record->quanity); ?></td>
</tr>
<tr>
  <th>Accession</th><td colspan="3"><?php echo $record->_print('accession'); ?></td>
</tr>
<tr>
  <th>Notes</th><td colspan="3"><?php echo scrub_out($record->notes); ?></td>
</tr>
<?php if (count($record->extra)) { 
      $site_fields = $record->site->get_setting('fields');
?>
<tr>
  <th colspan="4">
    <em>Custom Fields for <?php $record->site->_print('name'); ?></em>
  </th>
</tr>
<tr>
<?php 
$i=0;
$total = count($record->extra);
foreach ($record->extra as $name=>$field) { 
if ($i/2 == intval($i/2)) { echo "</tr><tr>"; }
$i++;
?>
  <th><?php echo scrub_out(str_replace('_',' ',$name)); ?></th>
  <td<?php if ($i == $total AND $i/2 != intval($i/2)) { ?> colspan="3"<?php } ?>>
    <?php // Format this based on the type of field... really just for boolean
      if ($site_fields['record' . $name]['type'] == 'boolean') {
        echo \UI\boolean_word($field);
      } else {
        echo scrub_out($field); 
      } 
    ?>
  </td>
<?php } // end foreach fields ?>
<?php } // if count fields ?>
</tr>
</table>
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Images</a></li>
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
