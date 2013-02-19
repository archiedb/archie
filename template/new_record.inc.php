<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php $record = Record::last_created(); ?>
<fieldset class="record">
<legend>LAST RECORD - Created by <?php echo scrub_out($record->user->username); ?> on <?php echo scrub_out(date("m/d/y",$record->created)); ?></legend>
<table class="table table-bordered">
<thead>
	<th>RN</th><th>Unit</th><th>Quad</th><th>Level</th>
</thead>
<tr>
	<td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo scrub_out($record->unit); ?></td>
	<td><?php echo scrub_out(quad::$values[$record->quad]); ?></td>
	<td><?php echo scrub_out($record->level); ?></td>
</tr>
</table>
</fieldset> 
<div class="content-block">
<fieldset class="record"><legend>CREATE RECORD - <?php echo Config::get('site'); ?></legend>
<form id="new_record" method="post" action="<?php echo Config::get('web_path'); ?>/new.php?action=create">
<table>
<tr style="vertical-align: top;">
<td>
	UNIT
</td><td>
	<select name="unit">
	<option value="-1">&nbsp;</option> 
	<?php foreach (unit::$values as $value) {
	        $is_selected = '';
	        if ($_POST['unit'] == $value) { $is_selected=" selected=\"selected\""; }
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
                if ($_POST['quad'] == $key) { $is_selected=" selected=\"selected\""; }
        ?>
                <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
        </select>
	<?php Error::display('quad'); ?>
</td>
</tr>
<tr>
<td>
	LEVEL
</td><td>
	<input name="level" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['level']); ?>" />
	<?php Error::display('level'); ?>
</td>
<td>
	RN
</td><td>
	<input name="station_index" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['station_index']); ?>" />
	<?php Error::display('station_index'); ?>
</td>
</tr>
<tr>
<td>
	FEATURE
</td><td>
	<input name="feature" type="text" class="textbox" size="15" value="<?php echo scrub_out($_POST['feature']); ?>" />
	<?php Error::display('feature'); ?>
</td>
<td valign="top" title="Lithostratigraphic Unit">
	L. U.
</td><td valign="top">
	<select name="lsg_unit">
	<?php foreach (lsgunit::$values as $key=>$name) {
	        $is_selected = '';
	        if ($_POST['lsg_unit'] == $key) { $is_selected=" selected=\"selected=\""; }
	?>
	        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
	<?php } ?>
	</select>
	<?php Error::display('lsg_unit'); ?>
</td>

</tr>
<tr>
<td>
	MATERIAL
</td>
<td>
<select id="material" name="material">
	<option value="">&nbsp;</option> 
	<?php $materials = Material::get_all(); ?>
	<?php foreach ($materials as $material) { 
		$isactive='';
		if ($_POST['material'] == $material->uid) { $isactive=' selected="selected"'; }
	?>
	<option value="<?php echo scrub_out($material->uid); ?>"<?php echo $isactive; ?>><?php echo scrub_out($material->name); ?></option>
	<?php } ?>
</select>
<?php Error::display('material'); ?>
<?php echo Ajax::select('material',Ajax::action('?action=show_class'),'classification'); ?>
</td>
<td>
	CLASSIFICATION
</td>
<td>
<select id="classification" name="classification">
<?php 
if ($_POST['material']) { $classes = Classification::get_from_material($_POST['material']); }
else { $classes = Classification::get_all(); } 
?>
<?php require_once Config::get('prefix') . '/template/show_class.inc.php'; ?>
</select>
<?php Error::display('classification'); ?>
</td>
</tr>
<tr>
<td valign="top">
	NOTES
</td><td colspan="3" valign="top">
	<?php Error::display('notes'); ?>
	<textarea placeholder="Notes..." name="notes" class="textbox" cols="40" rows="5"><?php echo scrub_out($_POST['notes']); ?></textarea>
</td>
</tr>
<tr>
<td colspan="2">
	<input type="submit" class="btn btn-primary" value="Create" />
</td>
</tr>
</table> 
</form>
</fieldset> 
</div> 
