<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="editspatial<?php echo $spatialdata->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Edit Spatial Point</h3>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/updatepoint">
        <div class="row">
        <div class="form-group">
          <label class="col-md-5 control-label" for="inputStationIndex">Total Station Index (RN)</label>
          <div class="col-md-5">
            <input class="form-control" id="inputStationIndex" name="station_index" type="text" value="<?php $spatialdata->_print('station_index'); ?>" />
          </div>
        </div>
        </div><div class="row">
        <div class="form-group">
          <label class="col-md-5 control-label" for="inputNorthing">Northing</label>
          <div class="col-md-5">
            <input class="form-control" id="inputNorthing" name="northing" type="text" value="<?php $spatialdata->_print('northing'); ?>" placeholder="000.000" />
          </div>
        </div>
        </div><div class="row">
        <div class="form-group">
          <label class="col-md-5 control-label" for="inputEasting">Easting</label>
          <div class="col-md-5">
            <input class="form-control" id="inputEasting" name="easting" type="text" value="<?php $spatialdata->_print('easting'); ?>" placeholder="000.000" />
          </div>
        </div>
        </div><div class="row">
        <div class="form-group">
          <label class="col-md-5 control-label" for="inputElevation">Elevation</label>
          <div class="col-md-5">
            <input class="form-control" id="inputElevation" name="elevation" type="text" value="<?php $spatialdata->_print('elevation'); ?>" placeholder="000.000" />
          </div>
        </div>
        </div><div class="row">
        <div class="form-group">
          <label class="col-md-5 control-label" for="inputNote">Note</label>
          <div class="col-md-5">
            <input class="form-control" id="inputNote" name="note" type="text" value="<?php $spatialdata->_print('note'); ?>" placeholder="Point Description" />
          </div>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Update Point</a>
        <input type="hidden" name="spatialdata_id" value="<?php $spatialdata->_print('uid'); ?>">
        <input type="hidden" name="krotovina_id" value="<?php $krotovina->_print('uid'); ?>">
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
