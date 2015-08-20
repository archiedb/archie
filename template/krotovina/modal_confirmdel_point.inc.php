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
      <table>
        <tr>
         <td>Station Index (RN)</td><td><strong><?php echo scrub_out($spatialdata->station_index); ?></strong></td>
        </tr><tr>
          <td>Northing</td><td><strong><?php echo scrub_out($spatialdata->northing); ?></strong></td>
        </tr><tr>
          <td>Easting</td><td><strong><?php echo scrub_out($spatialdata->easting); ?></strong></td>
        </tr><tr>
          <td>Elevation</td><td><strong><?php echo scrub_out($spatialdata->easting); ?></strong></td>
        </tr><tr>
          <td>Note</td><td><strong><?php echo scrub_out($spatialdata->note); ?></strong></td>
        </tr>
      </table>
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
