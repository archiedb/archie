<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<fieldset><legend>Edit Record - <?php echo scrub_out($record->site->name . '-' . $record->catalog_id); ?> <small><?php echo strlen($record->accession) ? 'Acc#' . $record->accession : ''; ?></small></legend>
<form class="form-horizontal" id="update_record" method="post" action="<?php echo Config::get('web_path'); ?>/records/update">
<div class="control-group span4<?php Error::display_class('level'); ?>">
  <label class="control-label" for="inputLevel"><abbr title="Unit:Quad:Level">Locus</abbr></label>
  <div class="controls">
    <?php
      $user_levels = Level::get_open_user_levels();
      if (!count($user_levels)) { $default_level_value = 'No Open Levels'; }
      else { $default_level_value = '&nbsp;'; }
    if (in_array($record->level->uid,$user_levels) OR Access::has('record','admin')) {
      // For Record admins add the current one even if it's not open
      if (!in_array($record->level->uid,$user_levels)) { $user_levels[] = $record->level->uid; }
    ?>
    <select id="inputLevel" name="level">
      <option value=""><?php echo $default_level_value; ?></option>
    <?php
      foreach ($user_levels as $level_uid) {
        $level = new Level($level_uid);
        $is_selected = '';
        if ($record->level->uid == $level_uid) { $is_selected=' selected="selected="'; }
    ?>
      <option value="<?php echo scrub_out($level_uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($level->name); ?></option>
    <?php } ?>
    </select>
    <?php } else { ?>
     <input id="levelText" type="text" name="textvalue" value="<?php echo scrub_out($record->level->name); ?>" disabled="disabled">
     <input id="inputLevel" name="level" type="hidden" value="<?php echo $record->level->uid; ?>" />
    <?php } ?>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('feature'); ?>">
  <label class="control-label" for="inputFeature">Feature</label>
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on">F-</span>
    	<input id="inputFeature" class="span2" name="feature" type="text" value="<?php echo scrub_out($record->feature->catalog_id); ?>" />
    </div>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('lsg_unit'); ?>">
  <label class="control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
  <div class="controls">
  	<select name="lsg_unit">
  	<?php foreach (lsgunit::$values as $key=>$name) { 
  		$is_selected = ''; 
  		if ($record->lsg_unit->uid == $key) { $is_selected=" selected=\"selected=\""; }
  	?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
  	<?php } ?>
	  </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('krotovina'); ?>">
  <label class="control-label" for="inputKrotovina">Krotovina</label>
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on">K-</span>
    	<input id="inputKrotovina" class="span2" name="krotovina" type="text" value="<?php echo scrub_out($record->krotovina->catalog_id); ?>" />
    </div>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('material'); ?>">
  <label class="control-label" for="material">Material</label>
  <div class="controls">
	  <select id="material" name="material">
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
<div class="control-group span4 offset1<?php Error::display_class('classification'); ?>">
  <label class="control-label" for="classification">Classification</label>
  <div class="controls">
	  <select id="classification" name="classification">
	  <?php 
    	$classes = Classification::get_from_material($record->material->uid);
  	 	$class_id = $record->classification->uid; 
      require_once \UI\template('/show_class'); 
	  ?>
	  </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('weight'); ?>">
  <label class="control-label" for="inputWeight">Weight</label>
  <div class="controls">
  	<div class="input-append">
    	<input id="inputWeight" class="span2" name="weight" type="text" value="<?php echo scrub_out($record->weight); ?>" />
    	<span class="add-on">grams</span>
    </div>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('height'); ?>">
  <label class="control-label" for="inputLength">Length</label>
  <div class="controls">
	  <div class="input-append">
    	<input id="inputLength" class="span2" name="height" type="text" value="<?php echo scrub_out($record->height); ?>" />
    	<span class="add-on">mm</span>
  	</div>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('width'); ?>">
  <label class="control-label" for="inputWidth">Width</label>
  <div class="controls">
  	<div class="input-append">
    	<input id="inputWidth" class="span2" name="width" type="text" value="<?php echo scrub_out($record->width); ?>" />
      <span class="add-on">mm</span>
  	</div>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('thickness'); ?>">
  <label class="control-label" for="inputThickness">Thickness</label>
  <div class="controls">
	  <div class="input-append">
    	<input id="inputThickness" class="span2" name="thickness" type="text" value="<?php echo scrub_out($record->thickness); ?>" />
    	<span class="add-on">mm</span>
  	</div>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('quanity'); ?>">
  <label class="control-label" for="inputQuanity">Quanity</label>
  <div class="controls">
  	<input id="inputQuanity" name="quanity" type="text" value="<?php echo scrub_out($record->quanity); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
    <input id="inputNorthing" type="text" name="northing" value="<?php echo scrub_out($record->northing); ?>"<?php echo $disabled; ?>>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('easting'); ?>">
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
    <input id="inputEasting" type="text" name="easting" value="<?php echo scrub_out($record->easting); ?>"<?php echo $disabled; ?>>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <?php $disabled = ($record->station_index == false) ? '' : ' disabled'; ?>
    <input id="inputElevation" type="text" name="elevation" value="<?php echo scrub_out($record->elevation); ?>"<?php echo $disabled; ?>>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('xrf_artifact_index'); ?>">
  <label class="control-label" for="inputXrfArtifactIndex">Artifact XRF</label>
  <div class="controls">
  	<input id="inputXrfArtifactIndex" type="text" name="xrf_artifact_index" value="<?php echo scrub_out($record->xrf_artifact_index); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('xrf_matrix_index'); ?>">
  <label class="control-label" for="inputXrfMatrixIndex">Matrix XRF</label>
  <div class="controls">
	  <input id="inputXrfMatrixIndex" name="xrf_matrix_index" type="text" value="<?php echo scrub_out($record->xrf_matrix_index); ?>" />
  </div>
</div> 
<div class="control-group span4<?php Error::display_class('notes'); ?>">
  <label class="control-label" for="inputNotes">Notes</label>
  <div class="controls">
  	<textarea name="notes" class="textbox" rows="5"><?php echo scrub_out($record->notes); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('station_index'); ?>">
  <label class="control-label" for="inputStationIndex">RN</label>
  <div class="controls">
	  <input id="inputStationIndex" name="station_index" type="text" value="<?php echo scrub_out($record->station_index); ?>" />
  </div>
</div>

<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
    <button class="btn btn-primary" type="submit">Update</button>
  </div>
</div>
</form>
</fieldset>
<fieldset><legend>Upload</legend>
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/records/upload">
 	<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
  <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
<div class="row">
  <div class="span4">
    <strong>Media</strong><br />
    <div class="fileupload fileupload-new" data-provides="fileupload">
      <div class="input-append">
      <div class="uneditable-input span3">
        <i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span>
      </div>
      <span class="btn btn-file"><span class="fileupload-new">Select</span>
      <span class="fileupload-exists">Change</span><input name="media" type="file" /></span>
      <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
    </div>
  </div>
</div>
<div class="span3 offset1"><strong>Description</strong><br /><input type="text" class="span4" name="description" /></div>
  <div class="span1 offset1">
    <br />
    <button class="btn btn-primary" type="submit">Upload</button>
  </div>
</div>
</form>
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
</fieldset>
