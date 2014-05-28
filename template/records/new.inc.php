<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<?php $record = Record::last_created(); ?>
<?php if ($record->uid) { ?>
  <h4>
    <a href="<?php echo Config::get('web_path'); ?>/records/view/<?php echo scrub_out($record->uid); ?>" class="btn btn-primary" >Last Record</a>
    <small>Created by <?php echo scrub_out($record->user->username); ?> on <?php echo scrub_out(date("d-M-y H:i:s",$record->created)); ?></small>
  </h4>
<table class="table table-bordered">
<thead>
	<th>RN</th><th>Level</th><th>Krotovina</th><th>Feature</th>
</thead>
<tr>
	<td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo \UI\record_link($record->level->uid,'level',$record->level->record); ?></td>
	<td><?php echo \UI\record_link($record->krotovina->uid,'krotovina',$record->krotovina->record); ?></td>
	<td><?php echo \UI\record_link($record->feature->uid,'feature',$record->feature->record); ?></td>
</tr>
</table>
<?php } ?>
<div class="page-header">
<h4>New Record - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_record" method="post" action="<?php echo Config::get('web_path'); ?>/records/create">
<div class="control-group span4<?php Error::display_class('level'); ?>">
  <label class="control-label" for="inputLevel">Level</label>
  <div class="controls">
    <?php 
      $user_levels = Level::get_open_user_levels(); 
      if (!count($user_levels)) { $default_level_value = 'No Open Levels'; }
      else { $default_level_value = '&nbsp;'; }
    ?>
    <select id="inputLevel" name="level">
      <option value=""><?php echo $default_level_value; ?></option>
    <?php 
      foreach ($user_levels as $level_uid) {
        $level = new Level($level_uid);
        $is_selected = '';
        if (isset($_POST['level'])) {
          if ($_POST['level'] == $level_uid) { $is_selected=' selected="selected="'; }
        }
    ?>
      <option value="<?php echo scrub_out($level_uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($level->record); ?></option>
    <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('lsg_unit'); ?>">
  <label class="control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
  <div class="controls">
	  <select name="lsg_unit">
      <?php foreach (lsgunit::$values as $key=>$name) {
	      $is_selected = '';
        if (isset($_POST['lsg_unit'])) { 
          if ($_POST['lsg_unit'] == $key) { $is_selected=" selected=\"selected=\""; }
        } 
      ?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="control-group span4<?php Error::display_class('feature'); ?>">
  <label class="control-label" for="inputFeature">Feature</label>
	<div class="controls">
    <div class="input-prepend">
      <span class="add-on">F-</span>
      <input id="inputFeature" class="span2" name="feature" type="text" value="<?php echo scrub_out($_POST['feature']); ?>" />
    </div>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('krotovina'); ?>">
  <label class="control-label" for="inputKrotovina">Krotovina</label>
	<div class="controls">
    <div class="input-prepend">
      <span class="add-on">K-</span>
      <input id="inputKrotovina" class="span2" name="krotovina" type="text" value="<?php echo scrub_out($_POST['krotovina']); ?>" />
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
		    $isactive='';
        if (isset($_POST['material'])) { 
      		if ($_POST['material'] == $material->uid) { $isactive=' selected="selected"'; }
        }
    	?>
    	<option value="<?php echo scrub_out($material->uid); ?>"<?php echo $isactive; ?>><?php echo scrub_out($material->name); ?></option>
    	<?php } ?>
    </select>
    <?php echo Ajax::select('material',Ajax::action('?action=show_class'),'classification'); ?>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('classification'); ?>">
  <label class="control-label" for="classification">Classification</label>
  <div class="controls">
    <select id="classification" name="classification">
    	<option value="">&nbsp;</option> 
      <?php 
      if (isset($_POST['material'])) { $classes = Classification::get_from_material($_POST['material']); }
      else { $classes = Classification::get_all(); } 
      ?>
      <?php require_once Config::get('prefix') . '/template/show_class.inc.php'; ?>
    </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" type="text" name="northing" value="<?php echo scrub_out($_POST['northing']); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>">
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" type="text" name="easting" value="<?php echo scrub_out($_POST['easting']); ?>">
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <input id="inputElevation" type="text" name="elevation" value="<?php echo scrub_out($_POST['elevation']); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('station_index'); ?>">
  <label class="control-label" for="inputStationIndex">RN</label>
  <div class="controls">
    <input id="inputStationIndex" name="station_index" type="text" value="<?php echo scrub_out($_POST['station_index']); ?>" />
  </div>
</div>
<div class="control-group span8<?php Error::display_class('notes'); ?>">
  <label class="control-label" for="inputNotes">Notes</label>
  <div class="controls">
	  <textarea placeholder="Notes..." rows="4" name="notes"><?php echo scrub_out($_POST['notes']); ?></textarea>
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
