<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<h3>Edit Record - <?php echo scrub_out($record->site->name . '-' . $record->catalog_id); ?> <small><?php echo strlen($record->accession) ? 'Acc#' . $record->accession : ''; ?></small></h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="update_record" method="post" action="<?php echo Config::get('web_path'); ?>/records/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel"><abbr title="Unit:Quad:Level">Locus</abbr></label>
    <div class="col-md-2">
      <?php
        $user_levels = Level::get_open_user_levels();
        if (!count($user_levels)) { $default_level_value = 'No Open Levels'; }
        else { $default_level_value = '&nbsp;'; }
        if (in_array($record->level->uid,$user_levels) OR Access::has('record','admin')) {
          // For Record admins add the current one even if it's not open
          if (!in_array($record->level->uid,$user_levels)) { $user_levels[] = $record->level->uid; }
        ?>
      <select class="form-control" id="inputLevel" name="level">
        <option value=""><?php echo $default_level_value; ?></option>
      <?php
        foreach ($user_levels as $level_uid) {
          $level = new Level($level_uid);
          $is_selected = '';
          if ($record->level->uid == $level_uid) { $is_selected=' selected="selected="'; }
      ?>
        <option value="<?php echo scrub_out($level_uid); ?>"<?php echo $is_selected; ?>><?php $level->_print('name'); ?></option>
      <?php } ?>
      </select>
      <?php } else { ?>
       <input id="levelText" type="text" name="textvalue" value="<?php $record->level->_print('name'); ?>" disabled="disabled">
       <input id="inputLevel" name="level" type="hidden" value="<?php $record->level->_print('uid'); ?>" />
      <?php } ?>
    </div>
    </div>
    <div class="<?php Error::form_class('feature'); ?>">
    <label class="col-md-2 control-label" for="inputFeature">Feature</label>
    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-addon">F-</span>
      	<input id="inputFeature" class="form-control" name="feature" type="text" value="<?php \UI\form_value(array('post'=>'feature','var'=>$record->feature->catalog_id)); ?>" />
      </div>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
    <div class="col-md-2">
    	<select class="form-control" name="lsg_unit">
    	<?php foreach (lsgunit::get_values() as $name) { 
    		$is_selected = ''; 
    		if ($record->lsg_unit->name == $name) { $is_selected=" selected=\"selected=\""; }
    	?>
        <option value="<?php echo scrub_out($name); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
    	<?php } ?>
  	  </select>
    </div>
    </div>
    <div class="<?php Error::form_class('krotovina'); ?>">
    <label class="col-md-2 control-label" for="inputKrotovina">Krotovina</label>
    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-addon">K-</span>
      	<input id="inputKrotovina" class="form-control" name="krotovina" type="text" value="<?php \UI\form_value(array('post'=>'krotovina','var'=>$record->krotovina->catalog_id)); ?>" />
      </div>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('material'); ?>">
    <label class="col-md-2 control-label" for="material">Material</label>
    <div class="col-md-2">
  	  <select class="form-control" id="material" name="material">
    		<option value="">&nbsp;</option> 
   		<?php $materials = Material::get_all(); ?>
   		<?php foreach ($materials as $material) { 
    			$is_selected = ''; 
    			if ($material->uid == $record->material->uid) { $is_selected = " selected=\"selected\""; } 
   		?>
    	  <option value="<?php echo scrub_out($material->uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($material->name); ?></option>
    	<?php } ?>
    	</select>
  	  <?php echo Ajax::select('material',Ajax::action('?action=show_class'),'classification'); ?>
    </div>
    </div>
    <div class="<?php Error::form_class('classification'); ?>">
    <label class="col-md-2 control-label" for="classification">Classification</label>
    <div class="col-md-2">
  	  <select class="form-control" id="classification" name="classification">
  	  <?php 
      	$classes = Classification::get_from_material($record->material->uid);
    	 	$class_id = $record->classification->uid; 
        require_once \UI\template('/show_class'); 
  	  ?>
  	  </select>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('weight'); ?>">
    <label class="col-md-2 control-label" for="inputWeight">Weight</label>
    <div class="col-md-2">
    	<div class="input-group">
      	<input id="inputWeight" class="form-control" name="weight" type="text" value="<?php \UI\form_value(array('post'=>'weight','var'=>$record->weight)); ?>" />
      	<span class="input-group-addon">grams</span>
      </div>
    </div>
    </div>
    <div class="<?php Error::form_class('height'); ?>">
    <label class="col-md-2 control-label" for="inputLength">Length</label>
    <div class="col-md-2">
  	  <div class="input-group">
      	<input class="form-control" id="inputLength" name="height" type="text" value="<?php \UI\form_value(array('post'=>'height','var'=>$record->height)); ?>" />
      	<span class="input-group-addon">mm</span>
    	</div>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('width'); ?>">
    <label class="col-md-2 control-label" for="inputWidth">Width</label>
    <div class="col-md-2">
    	<div class="input-group">
      	<input id="inputWidth" class="form-control" name="width" type="text" value="<?php \UI\form_value(array('post'=>'width','var'=>$record->width)); ?>" />
        <span class="input-group-addon">mm</span>
    	</div>
    </div>
    </div>
    <div class="<?php Error::form_class('thickness'); ?>">
    <label class="col-md-2 control-label" for="inputThickness">Thickness</label>
    <div class="col-md-2">
  	  <div class="input-group">
      	<input id="inputThickness" class="form-control" name="thickness" type="text" value="<?php \UI\form_value(array('post'=>'thickness','var'=>$record->thickness)); ?>" />
      	<span class="input-group-addon">mm</span>
    	</div>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('quanity'); ?>">
    <label class="col-md-2 control-label" for="inputQuanity">Quanity</label>
    <div class="col-md-2">
    	<input class="form-control" id="inputQuanity" name="quanity" type="text" value="<?php \UI\form_value(array('post'>'quanity','var'=>$record->quanity)); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
      <input class="form-control" id="inputNorthing" type="text" name="northing" value="<?php \UI\form_value(array('post'=>'northing','var'=>$record->northing)); ?>"<?php echo $disabled; ?>>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
      <input class="form-control" id="inputEasting" type="text" name="easting" value="<?php \UI\form_value(array('post'=>'easting','var'=>$record->easting)); ?>"<?php echo $disabled; ?>>
    </div>
    </div>
    <div class="<?php Error::form_class('elevation'); ?>">
    <label class="col-md-2 control-label" for="inputElevation">Elevation</label>
    <div class="col-md-2">
      <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
      <input class="form-control" id="inputElevation" type="text" name="elevation" value="<?php \UI\form_value(array('post'=>'elevation','var'=>$record->elevation)); ?>"<?php echo $disabled; ?>>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('xrf_artifact_index'); ?>">
    <label class="col-md-2 control-label" for="inputXrfArtifactIndex">Artifact XRF</label>
    <div class="col-md-2">
    	<input class="form-control" id="inputXrfArtifactIndex" type="text" name="xrf_artifact_index" value="<?php \UI\form_value(array('post'=>'xrf_artifact_index','var'=>$record->xrf_artifact_index)); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('xrf_matrix_index'); ?>">
    <label class="col-md-2 control-label" for="inputXrfMatrixIndex">Matrix XRF</label>
    <div class="col-md-2">
  	  <input class="form-control" id="inputXrfMatrixIndex" name="xrf_matrix_index" type="text" value="<?php \UI\form_value(array('post'=>'xrf_matrix_index','var'=>$record->xrf_matrix_index)); ?>" />
    </div>
    </div> 
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('notes'); ?>">
    <label class="col-md-2 control-label" for="inputNotes">Notes</label>
    <div class="col-md-2">
    	<textarea name="notes" class="form-control" rows="5"><?php \UI\form_value(array('post'=>'notes','var'=>$record->notes)); ?></textarea>
    </div>
    </div>
    <div class="<?php Error::form_class('station_index'); ?>">
    <label class="col-md-2 control-label" for="inputStationIndex">Station Index (RN)</label>
    <div class="col-md-2">
  	  <input class="form-control" id="inputStationIndex" name="station_index" type="text" value="<?php \UI\form_value(array('post'=>'station_index','var'=>$record->station_index)); ?>" />
    </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="form-group">
    <div class="col-md-10 col-md-offset-2">
      <input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
      <button class="btn btn-primary" type="submit">Update</button>
    </div>
  </div>
</div>
</form>
<h4>Upload</h4><hr />
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/records/upload">
 	<input type="hidden" name="record_id" value="<?php $record->_print('uid'); ?>" />
  <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
<div class="row">
  <div class="form-group">
    <label class="col-md-2 control-label" for="inputDescription">Description</label>
    <div class="col-md-4">
      <input type="text" class="form-control" name="description" />
    </div>
    <div class="col-md-4">
      <input type="file" name="media" class="filestyle" data-buttonText="" data-buttonbefore="true">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" type="submit">Upload</button>
    </div>
  </div>
<div>
</form>
<br />
<div class="row">
<!-- Images/3dModels/Media -->
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Pictures</a></li>
  <li><a href="#3dmodel" data-toggle="tab">3D Models</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/records/images'); ?>
  </div> <!-- End Picture tab -->
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/records/3dmodel'); ?>
  </div> <!-- End 3dModel Tab -->
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/records/media'); ?>
  </div> <!-- End Media tab pane -->
</div> <!-- End Container --> 
