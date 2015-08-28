<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirmdel_<?php echo $spatialdata->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Remove Spatial Point</h3>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to remove this spatial point from <?php $krotovina->_print('record'); ?>?</p>
      <p>
        <table class="table table-striped">
          <tr>
           <td>Station Index (RN)</td><td><strong><?php $spatialdata->_print('station_index'); ?></strong></td>
          </tr><tr>
            <td>Northing</td><td><strong><?php $spatialdata->_print('northing'); ?></strong></td>
          </tr><tr>
            <td>Easting</td><td><strong><?php $spatialdata->_print('easting'); ?></strong></td>
          </tr><tr>
            <td>Elevation</td><td><strong><?php $spatialdata->_print('elevation'); ?></strong></td>
          </tr><tr>
            <td>Note</td><td><strong><?php $spatialdata->_print('note'); ?></strong></td>
          </tr>
        </table>
      </p>
    </div>
    <div class="modal-footer">
    <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/delpoint">
      <button type="submit" class="btn btn-danger">Remove Point</a>
      <input type="hidden" name="krotovina_id" value="<?php $krotovina->_print('uid'); ?>">
      <input type="hidden" name="uid" value="<?php $spatialdata->_print('uid'); ?>">
      <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
</div>
