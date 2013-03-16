<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php Event::display(); ?>
<fieldset class="record"><legend>Edit Record - <?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></legend>
<?php Error::display('general'); ?>
<form class="form-horizontal" id="update_record" method="post" action="<?php echo Config::get('web_path'); ?>/records/update">
<table>
<tr>
<td>
	UNIT
</td><td>
	<select name="unit">
	<?php foreach (unit::$values as $value) { 
		$is_selected = ''; 
		if ($record->unit == $value) { $is_selected=" selected=\"selected\""; } 
	?>
		<option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option> 
	<?php } ?>
	</select>
	<?php Error::display('unit'); ?>
</td>
<td>
	QUAD
</td><td>
        <select name="quad">
        <option value="">&nbsp;</option>
        <?php foreach (quad::$values as $key=>$value) {
                        $is_selected = '';
                if ($record->quad->uid == $key) { $is_selected=" selected=\"selected\""; }
        ?>
                <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
        </select>
        <?php Error::display('quad'); ?>
</td>
</tr><tr>
<td>
	LEVEL
</td><td>
	<input name="level" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->level); ?>" />
	<?php Error::display('level'); ?>
</td>
<td>
	MATRIX XRF #
</td><td>
	<input name="xrf_matrix_index" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->xrf_matrix_index); ?>" />
	<?php Error::display('xrf_matrix_index'); ?>
</td>
</tr>

<tr>
<td>
	FEATURE
</td><td>
	<input name="feature" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->feature); ?>" />
	<?php Error::display('feature'); ?>
</td>
<td valign="top">
	ARTIFACT XRF #
</td><td>
	<input type="text" class="textbox" size="10" name="xrf_artifact_index" value="<?php echo scrub_out($record->xrf_artifact_index); ?>" />
	<?php Error::display('xrf_artifact_index'); ?>
</td>
</tr>

<tr>
<td title="Lithostratigraphic Unit">
	L. U.
</td><td>
	<select name="lsg_unit">
	<?php foreach (lsgunit::$values as $key=>$name) { 
		$is_selected = ''; 
		if ($record->lsg_unit->uid == $key) { $is_selected=" selected=\"selected=\""; }
	?>
	        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
	<?php } ?>
	</select>
	<?php Error::display('lsg_unit'); ?>
</td>
<td>
	RN
</td><td>
	<input name="station_index" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->station_index); ?>" />
	<?php Error::display('station_index'); ?>
</td>
</tr>

<tr>
<td>
	MATERIAL
</td><td>
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
	<?php Error::display('material'); ?>
	<?php echo Ajax::select('material',Ajax::action('?action=show_class'),'classification'); ?>
</td>
<td>
	CLASSIFICATION
</td><td>
	<select id="classification" name="classification">
		<?php 
		$classes = Classification::get_from_material($record->material->uid);
	 	$class_id = $record->classification->uid; 
		require_once Config::get('prefix') . '/template/show_class.inc.php'; 
		?>
	</select>
	<?php Error::display('classification'); ?>
</td>
</tr>

<tr>
<td>
	WEIGHT
</td><td>
	<div class="input-append">
	<input class="span2" name="weight" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->weight); ?>" />
	<span class="add-on">grams</span>
	</div>
	<?php Error::display('weight'); ?>
</td>
<td>
	LENGTH
</td><td>
	<div class="input-append">
	<input class="span2" name="height" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->height); ?>" />
	<span class="add-on">mm</span>
	</div>
	<?php Error::display('height'); ?>
</td>
</tr>

<tr>
<td>
	WIDTH
</td><td>
	<div class="input-append">
	<input class="span2" name="width" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->width); ?>" />
	<span class="add-on">mm</span>
	</div>
	<?php Error::display('width'); ?>
</td>
<td>
	THICKNESS
</td><td>
	<div class="input-append">
	<input class="span2" name="thickness" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->thickness); ?>" />
	<span class="add-on">mm</span>
	</div>
	<?php Error::display('thickness'); ?>
</td>
</tr>
<tr>
<td>
	QUANTITY
</td><td>
	<input name="quanity" type="text" class="textbox" size="10" value="<?Php echo scrub_out($record->quanity); ?>" />
	<?php Error::display('quanity'); ?>
</td>
<td>
	NOTES
</td><td>
	<textarea name="notes" class="textbox" cols="25" rows="5"><?php echo scrub_out($record->notes); ?></textarea>
	<?php Error::display('notes'); ?>
</td>
</tr>

</table> 
<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
<button class="btn btn-primary" type="submit">Update</button>
</form>
<?php Error::display('upload'); ?>
<?php Error::display('media'); ?>
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Pictures</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/records/upload_image">
    	<input type="hidden" name="MAX_FILE_SIZE" value="15728640" />
    	<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
    <div class="row">
      <div class="span4">
        <strong>Image</strong><br />
        <div class="fileupload fileupload-new" data-provides="fileupload">
          <div class="input-append">
            <div class="uneditable-input span3">
              <i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span>
            </div>
            <span class="btn btn-file"><span class="fileupload-new">Select</span>
            <span class="fileupload-exists">Change</span><input name="image" type="file" /></span>
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
    <hr />
    <?php require_once \UI\template('/records/images'); ?>
  </div> <!-- End Picture tab -->
  <div class="tab-pane" id="media">
    <form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/records/upload_media">
    	<input type="hidden" name="MAX_FILE_SIZE" value="15728640" />
    	<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
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
    <hr />
    <?php require_once \UI\template('/records/media'); ?>
  </div> <!-- End Media tab pane -->
</div> <!-- End Container --> 
