<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
require_once 'template/menu.inc.php';
?>
<?php $record = Record::last_created(); ?>
<?php if ($record->uid) { ?>
  <h4>
    <a href="<?php echo Config::get('web_path'); ?>/records/view/<?php echo scrub_out($record->uid); ?>" class="btn btn-primary" >Last Record</a>
    <small>Created by <?php echo scrub_out($record->user->username); ?> on <?php echo scrub_out(date("d-M-y H:i:s",$record->created)); ?></small>
  </h4>
<table class="table table-bordered">
<thead>
	<th>RN</th><th><abbr title="Unit:Quad:Level">Locus</abbr></th><th>Krotovina</th><th>Feature</th>
</thead>
<tr>
	<td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo \UI\record_link($record->level->uid,'level',$record->level->name); ?></td>
	<td><?php echo \UI\record_link($record->krotovina->uid,'krotovina',$record->krotovina->record); ?></td>
	<td><?php echo \UI\record_link($record->feature->uid,'feature',$record->feature->record); ?></td>
</tr>
</table>
<?php } ?>
<div class="page-header">
<h4>New Record - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h4>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_record" method="post" action="<?php echo Config::get('web_path'); ?>/records/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel"><abbr title="Unit:Quad:Level">Locus</abbr></label>
    <div class="col-md-2 <?php Error::display_class('level'); ?>">
      <?php 
        $user_levels = Level::get_open_user_levels(); 
        if (!count($user_levels)) { $default_level_value = 'No Open Levels'; }
        else { $default_level_value = '&nbsp;'; }
      ?>
      <select id="inputLevel" class="form-control" name="level">
        <option value=""><?php echo $default_level_value; ?></option>
      <?php 
      foreach ($user_levels as $level_uid) {
          $level = new Level($level_uid);
          $is_selected = '';
          if (isset($_POST['level'])) {
            if ($_POST['level'] == $level_uid) { $is_selected=' selected="selected="'; }
          }
      ?>
        <option value="<?php echo scrub_out($level_uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($level->name); ?></option>
      <?php } ?>
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
    <div class="<?php Error::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
    <div class="col-md-2 <?php Error::display_class('lsg_unit'); ?>">
	    <select class="form-control" name="lsg_unit">
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
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('feature'); ?>">
    <label class="col-md-2 control-label" for="inputFeature">Feature</label>
    <div class="col-md-2 <?php Error::display_class('feature'); ?>">
      <div class="input-group">
        <span class="input-group-addon">F-</span>
        <input id="inputFeature" class="form-control" name="feature" type="text" value="<?php \UI\form_value('feature'); ?>" />
      </div>
    </div> <!-- ERROR CHECK -->
    </div>
    <div class="<?php Error::form_class('krotovina'); ?>">
    <label class="col-md-2 control-label" for="inputKrotovina">Krotovina</label>
    <div class="col-md-2 <?php Error::display_class('krotovina'); ?>">
      <div class="input-group">
        <span class="input-group-addon">K-</span>
        <input id="inputKrotovina" class="form-control" name="krotovina" type="text" value="<?php \UI\form_value('krotovina'); ?>" />
      </div>
    </div>
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('material'); ?>">
    <label class="col-md-2 control-label" for="material">Material</label>
    <div class="col-md-2 <?php Error::display_class('material'); ?>">
      <select class="form-control" id="material" name="material">
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
    </div> <!-- ERROR CHECK -->
    <div class="<?php Error::form_class('classification'); ?>">
    <label class="col-md-2 control-label" for="classification">Classification</label>
    <div class="col-md-2 <?php Error::display_class('classification'); ?>">
      <select class="form-control" id="classification" name="classification">
      	<option value="">&nbsp;</option> 
        <?php 
        if (!empty($_POST['material'])) { $classes = Classification::get_from_material($_POST['material']); }
        else { $classes = Classification::get_all(); } 
        ?>
        <?php require_once Config::get('prefix') . '/template/show_class.inc.php'; ?>
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2 <?php Error::display_class('northing'); ?>">
      <input id="inputNorthing" class="form-control" type="text" name="northing" value="<?php \UI\form_value('northing'); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2 <?php Error::display_class('easting'); ?>">
      <input id="inputEasting" class="form-control" type="text" name="easting" value="<?php \UI\form_value('easting'); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elevation'); ?>">
    <label class="col-md-2 control-label" for="inputElevation">Elevation</label>
    <div class="col-md-2">
      <input id="inputElevation" class="form-control" type="text" name="elevation" value="<?php \UI\form_value('elevation'); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('station_index'); ?>">
    <label class="col-md-2 control-label" for="inputStationIndex">RN</label>
    <div class="col-md-2">
      <input id="inputStationIndex" class="form-control" name="station_index" type="text" value="<?php \UI\form_value('station_index'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('notes'); ?>">
      <label class="col-md-2 control-label" for="inputNotes">Notes</label>
      <div class="col-md-6">
    	  <textarea placeholder="Notes..." class="form-control" rows="4" name="notes"><?php \UI\form_value('notes'); ?></textarea>
      </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
  <div class="col-md-6 col-md-offset-2">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
  </div>
</div>
</form>
