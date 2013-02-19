<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="content-block">
<fieldset class="record"><legend>Edit Record - <?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></legend>
<form id="update_record" method="post" action="<?php echo Config::get('web_path'); ?>/new.php?action=update">
<table class="record">

<tr>
<td>
	UNIT
</td><td>
	<?php Error::display('unit'); ?>
	<select name="unit">
	<?php foreach (unit::$values as $value) { 
		$is_selected = ''; 
		if ($record->unit == $value) { $is_selected=" selected=\"selected\""; } 
	?>
		<option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option> 
	<?php } ?>
	</select>
</td>
<td>
	QUAD
</td><td>
        <?php Error::display('quad'); ?>
        <select name="quad">
        <option value="">&nbsp;</option>
        <?php foreach (quad::$values as $key=>$value) {
                        $is_selected = '';
                if ($record->quad == $key) { $is_selected=" selected=\"selected\""; }
        ?>
                <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
        </select>

</td>
</tr><tr>
<td>
	LEVEL
</td><td>
	<?php Error::display('level'); ?>
	<input name="level" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->level); ?>" />
</td>
<td>
	MATRIX XRF #
</td><td>
	<?php Error::display('xrf_matrix_index'); ?>
	<input name="xrf_matrix_index" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->xrf_matrix_index); ?>" />
</td>
</tr>

<tr>
<td>
	FEATURE
</td><td>
	<?php Error::display('feature'); ?>
	<input name="feature" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->feature); ?>" />
</td>
<td valign="top">
	ARTIFACT XRF #
</td><td>
	<?php Error::display('xrf_artifact_index'); ?>
	<input type="text" class="textbox" size="10" name="xrf_artifact_index" value="<?php echo scrub_out($record->xrf_artifact_index); ?>" />
</td>
</tr>

<tr>
<td title="Lithostratigraphic Unit">
	L. U.
</td><td>
	<?php Error::display('lsg_unit'); ?>
	<select name="lsg_unit">
	<?php foreach (lsgunit::$values as $key=>$name) { 
		$is_selected = ''; 
		if ($record->lsg_unit == $key) { $is_selected=" selected=\"selected=\""; }
	?>
	        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
	<?php } ?>
	</select>
</td>
<td>
	RN
</td><td>
	<?php Error::display('station_index'); ?>
	<input name="station_index" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->station_index); ?>" />
</td>
</tr>

<tr>
<td>
	MATERIAL
</td><td>
	<?php Error::display('material'); ?>
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
	<?php echo Ajax::observe('material','change',Ajax::action('?action=show_class','material','update_record'),1); ?>
</td>
<td>
	DESCRIPTION
</td><td>
	<?php Error::display('classification'); ?>
	<div id="classification_select">
		<?php 
		$classes = Classification::get_from_material($record->material->uid);
	 	$class_id = $record->classification->uid; 
		require_once Config::get('prefix') . '/template/show_class.inc.php'; 
		?>
	</div>
</td>
</tr>

<tr>
<td>
	WEIGHT (GRAMS)
</td><td>
	<?php Error::display('weight'); ?>
	<input name="weight" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->weight); ?>" />
</td>
<td>
	LENGTH (mm)
</td><td>
	<?php Error::display('height'); ?>
	<input name="height" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->height); ?>" />
</td>
</tr>

<tr>
<td>
	WIDTH (mm)
</td><td>
	<?php Error::display('width'); ?>
	<input name="width" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->width); ?>" />
</td>
<td>
	THICKNESS (mm)
</td><td>
	<?php Error::display('thickness'); ?>
	<input name="thickness" type="text" class="textbox" size="10" value="<?php echo scrub_out($record->thickness); ?>" />
</td>
</tr>

<tr>
<td>
	QUANTITY
</td><td>
	<?php Error::display('quanity'); ?>
	<input name="quanity" type="text" class="textbox" size="10" value="<?Php echo scrub_out($record->quanity); ?>" />
</td>
<td>
	NOTES
</td><td>
	<?php Error::display('notes'); ?>
	<textarea name="notes" class="textbox" cols="25" rows="5"><?php echo scrub_out($record->notes); ?></textarea>
</td>
</tr>

</table> 
<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
<?php Error::display('general'); ?>
<input type="submit" value="Save Changes" />
</form>
<fieldset class="attachment">
<legend>Images</legend>
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/new.php?action=upload_image">
	<input type="hidden" name="MAX_FILE_SIZE" value="15728640" />
	<input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
	<input type="file" class="textbox" name="image" />
	<input type="submit" value="Attach Image" />
</form>

<?php 
	$images = $record->get_images(); 
	foreach ($images as $image) { 
	$i++; 
?>

<div class="image-block">
	<a target="_blank" href="<?php echo Config::get('web_path'); ?>/image.php?content_id=<?php echo scrub_out($image['uid']); ?>">
	<img src="<?php echo Config::get('web_path'); ?>/image.php?content_id=<?php echo scrub_out($image['uid']);?>&thumb=true" alt="Image <?php echo $i; ?>" />
	</a>
</div>
<?php } ?>
<?php Error::display('upload'); ?>
</fieldset> 

</fieldset>
</div><!-- End content block -->
