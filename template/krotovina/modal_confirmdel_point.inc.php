<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirmdel_<?php echo scrub_out($spatialdata->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/delpoint">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Remove Spatial Point</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure you want to remove this spatial point from <?php echo scrub_out($krotovina->record); ?>?</p>
    <p>
      <div class="span2">Station Index (RN)</div><div class="span3"><strong><?php echo scrub_out($spatialdata->station_index); ?></strong></div>
      <div class="span2">Northing</div><div class="span3"><strong><?php echo scrub_out($spatialdata->northing); ?></strong></div>
      <div class="span2">Easting</div><div class="span3"><strong><?php echo scrub_out($spatialdata->easting); ?></strong></div>
      <div class="span2">Elevation</div><div class="span3"><strong><?php echo scrub_out($spatialdata->easting); ?></strong></div>
      <div class="span2">Note</div><div class="span3"><strong><?php echo scrub_out($spatialdata->note); ?></strong></div>
    </p>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-danger">Remove Point</a>
    <input type="hidden" name="krotovina_id" value="<?php echo scrub_out($krotovina->uid); ?>">
    <input type="hidden" name="uid" value="<?php echo scrub_out($spatialdata->uid); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
