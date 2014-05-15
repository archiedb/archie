<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="addspatial" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/addpoint">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Add Spatial Point</h3>
  </div>
  <div class="modal-body">
    <div class="control-group span4">
      <label class="control-label" for="inputRN">Total Station Index (RN)</label>
      <div class="controls">
        <input id="inputRN" name="rn" type="text" value="" placeholder="Numeric Total Station Index" />
      </div>
    </div>
    <div class="control-group span4">
      <label class="control-label" for="inputNorthing">Northing</label>
      <div class="controls">
        <input id="inputNorthing" name="northing" type="text" value="" placeholder="000.000" />
      </div>
    </div>
    <div class="control-group span4">
      <label class="control-label" for="inputEasting">Easting</label>
      <div class="controls">
        <input id="inputEasting" name="easting" type="text" value="" placeholder="000.000" />
      </div>
    </div>
    <div class="control-group span4">
      <label class="control-label" for="inputElevation">Elevation</label>
      <div class="controls">
        <input id="inputElevation" name="elevation" type="text" value="" placeholder="000.000" />
      </div>
    </div>
    <div class="control-group span4">
      <label class="control-label" for="inputNote">Note</label>
      <div class="controls">
        <input id="inputNote" name="note" type="text" value="" placeholder="Point Description" />
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-success">Add Point</a>
    <input type="hidden" name="krotovina_id" value="<?php echo scrub_out($krotovina->uid); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
