<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php $record = Record::last_created(); ?>
<fieldset class="record"><legend>LAST RECORD</legend>
<table class="previous_record" cellspacing="5" border="0">
<tr>
	<th>RN</th><th>Unit</th><th>Quad</th><th>Level</th>
</tr>
<tr>
	<td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo scrub_out($record->unit); ?></td>
	<td><?php echo scrub_out(quad::$values[$record->quad]); ?></td>
	<td><?php echo scrub_out($record->level); ?></td>
</tr>
</table>
</fieldset> 
<fieldset class="record"><legend>CREATE RECORD - <?php echo Config::get('site'); ?></legend>
<form id="new_record" method="post" action="<?php echo Config::get('web_path'); ?>/new.php?action=create">
<table class="record" cellspacing="5" border=0>
<tr>
<td>
	UNIT
</td><td>
	<?php Error::display('unit'); ?>
	<select name="unit">
	<option value="-1">&nbsp;</option> 
	<?php foreach (unit::$values as $value) {
	        $is_selected = '';
	        if ($_POST['unit'] == $value) { $is_selected=" selected=\"selected\""; }
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
                if ($_POST['quad'] == $key) { $is_selected=" selected=\"selected\""; }
        ?>
                <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
        </select>
</td>
</tr>
<tr>
<td>
	LEVEL
</td><td>
	<?php Error::display('level'); ?>
	<input name="level" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['level']); ?>" />
</td>
<td>
	RN
</td><td>
	<?php Error::display('station_index'); ?>
	<input name="station_index" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['station_index']); ?>" />
</td>
</tr>
<tr>
<td>
	FEATURE
</td><td>
	<?php Error::display('feature'); ?>
	<input name="feature" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['feature']); ?>" />
</td>
<td valign="top" title="Lithostratigraphic Unit">
	L. U.
</td><td valign="top">
	<?php Error::display('lsg_unit'); ?>
	<select name="lsg_unit">
	<?php foreach (lsgunit::$values as $key=>$name) {
	        $is_selected = '';
	        if ($_POST['lsg_unit'] == $key) { $is_selected=" selected=\"selected=\""; }
	?>
	        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
	<?php } ?>
	</select>
</td>

</tr>
<tr>
<td>
	MATERIAL
</td>
<td>
<?php Error::display('material'); ?>
<select id="material" name="material">
	<option value="">&nbsp;</option> 
	<?php $materials = Material::get_all(); ?>
	<?php foreach ($materials as $material) { ?>
	<option value="<?php echo scrub_out($material->uid); ?>"><?php echo scrub_out($material->name); ?></option>
	<?php } ?>
</select>
<?php echo Ajax::observe('material','change',Ajax::action('?action=show_class','material','new_record'),1); ?>
</td>
<td>
	CLASSIFICATION
</td>
<td>
<?php Error::display('classification'); ?>
<div id="classification_select">
<?php $classes = Classification::get_all(); ?>
<?php require_once Config::get('prefix') . '/template/show_class.inc.php'; ?>
</div>
</td>
</tr>
<tr>
<td valign="top">
	NOTES
</td><td valign="top">
	<?php Error::display('notes'); ?>
	<textarea name="notes" class="textbox" cols="25" rows="5"><?php echo scrub_out($_POST['notes']); ?></textarea>
</td>
</tr>
<!--
<h3>WEIGHT IN GRAMS</h3>
<?php Error::display('weight'); ?>
<input name="weight" type="textbox" value="<?php echo scrub_out($_POST['weight']); ?>" />
<h3>LENGTH IN CM</h3>
<?php Error::display('height'); ?>
<input name="height" type="textbox" value="<?php echo scrub_out($_POST['height']); ?>" />
<h3>WIDTH IN CM</h3>
<?php Error::display('width'); ?>
<input name="width" type="textbox" value="<?php echo scrub_out($_POST['width']); ?>" />
<h3>THICKNESS IN CM</h3>
<?php Error::display('thickness'); ?>
<input name="thickness" type="textbox" value="<?php echo scrub_out($_POST['thickness']); ?>" />
<h3>QUANITY</h3>
<?php Error::display('quanity'); ?>
<input name="quanity" type="textbox" value="<?Php echo scrub_out($_POST['quanity']); ?>" />
<h3>XRF ARTIFACT INDEX</h3>
<?php Error::display('xrf_artifact_index'); ?>
<input type="textbox" name="xrf_artifact_index" value="<?php echo scrub_out($_POST['xrf_artifact_index']); ?>" />
--> 
</table> 
<input type="submit" value="Save" />
</form>
</fieldset> 
