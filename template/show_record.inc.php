<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="text-right">
  <a href="<?php echo Config::get('web_path'); ?>/records/edit/<?php echo scrub_out($record->uid); ?>" class="btn btn-primary">Edit Record</a>
  <a href="<?php echo Config::get('web_path'); ?>/records/print/<?php echo scrub_out($record->uid); ?>/ticket" class="btn btn-success">Print Ticket</a>
</p>
<div class="content-block">
<div class="record-header-left">
<h3><?php echo $record->site . '-' . $record->catalog_id; ?></h3>
  Entered by <?php echo $record->user->username; ?> on <?php echo date("r",$record->created); ?></p>
</div>
<p class="text-right record-header-right">
<img class="img-polaroid" src="<?php echo Config::get('web_path'); ?>/media/qrcode/<?php echo scrub_out($record->uid); ?>" />
</p>
<fieldset class="record">
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
  <th>MATRIX XRF #</th><td><?php echo scrub_out($record->xrf_matrix_index); ?></td>
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
  <th>QUANTITY</th><td><?php echo scrub_out($record->quanity); ?></td>
  <th>MATERIAL</th><td><?php echo scrub_out($record->material->name); ?></td>
</tr>
<tr>
  <th>CLASSIFICATION</th><td><?php echo scrub_out($record->classification->name); ?></td>
  <th>ARTIFACT XRF #</th><td><?php echo scrub_out($record->xrf_artifact_index); ?></td>
</tr>
<tr>
  <th valign="top">NOTES</th><td colspan="3"><?php echo scrub_out($record->notes); ?></td>
</tr>
</table>
<fieldset class="attachment">
<legend>Item Pictures</legend>
<?php
        $images = $record->get_images();
        foreach ($images as $image) {
        $i++;
?>

<div class="image-block">
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/media/record/<?php echo scrub_out($image['uid']); ?>"><img src="<?php echo Config::get('web_path'); ?>/media/thumb/<?php echo scrub_out($image['uid']);?>" alt="Image <?php echo $i; ?>" /></a>
</div>
<?php } if (!count($images)) { ?>
<h4>No Images Found</h4>
<?php } ?>
</fieldset>
</fieldset> 
</div><!-- End content block --> 
