<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
 <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>" class="btn btn-primary">View Level</a>
  <?php if (!$level->closed) { ?>
  <a href="<?php echo Config::get('web_path'); ?>/level/checkclose/<?php echo scrub_out($level->uid); ?>" class="btn btn-danger">Close</a>
  <?php } ?>
</p>
<h3>Edit Level - <?php echo scrub_out($level->site . '-' . $level->record); ?></h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<fieldset>
<form class="form-horizontal" id="update_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/update">
<div class="control-group span4<?php Error::display_class('unit'); ?>">
  <label class="control-label" for="inputUnit">Unit</label>
  <div class="controls">
	  <select name="unit">
  	<?php foreach (unit::$values as $value) { 
	  	$is_selected = ''; 
  		if ($level->unit == $value) { $is_selected=" selected=\"selected\""; } 
  	?>
  		<option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option> 
  	<?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('quad'); ?>">
  <label class="control-label" for="inputQuad">Quad</label>
  <div class="controls">
    <select id="inputQuad" name="quad">
      <option value="">&nbsp;</option>
      <?php foreach (quad::$values as $key=>$value) {
        $is_selected = '';
        if ($level->quad->uid == $key) { $is_selected=" selected=\"selected\""; }
      ?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
     <?php } ?>
   </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('level'); ?>">
  <label class="control-label" for="inputLevel">Level</label>
  <div class="controls">
  	<input id="inputLevel" name="record" type="text" value="<?php echo scrub_out($level->record); ?>" />
  </div>
</div> 
<div class="control-group span4 offset1<?php Error::display_class('lsg_unit'); ?>">
  <label class="control-label" for="inputLsgUnit">L. U.</label>
  <div class="controls">
  	<select name="lsg_unit">
  	<?php foreach (lsgunit::$values as $key=>$name) { 
  		$is_selected = ''; 
  		if ($level->lsg_unit->uid == $key) { $is_selected=" selected=\"selected=\""; }
  	?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
  	<?php } ?>
	  </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" type="text" name="northing" value="<?php echo scrub_out($level->northing); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>">
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" type="text" name="easting" value="<?php echo scrub_out($level->easting); ?>">
  </div>
</div>
<div class="control-group span10">
  <h4>Elevations</h4><hr />
</div>
<div class="control-group span4<?php Error::display_class('elv_nw_start'); ?>">
  <label class="control-label" for="inputElvNWStart">NW Start</label>
  <div class="controls">
    <input id="inputElvNWStart" name="elv_nw_start" type="text" value="<?php echo scrub_out($level->elv_nw_start); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_nw_finish'); ?>">
  <label class="control-label" for="inputElvNWFinish">NW Finish</label>
  <div class="controls">
    <input id="inputElvNWFinish" name="elv_nw_finish" type="text" value="<?php echo scrub_out($level->elv_nw_finish); ?>">
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elv_ne_start'); ?>">
  <label class="control-label" for="inputElvNEStart">NE Start</label>
  <div class="controls">
    <input id="inputElvNEStart" name="elv_ne_start" type="text" value="<?php echo scrub_out($level->elv_ne_start); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_ne_finish'); ?>">
  <label class="control-label" for="inputElvNEFinish">NE Finish</label>
  <div class="controls">
    <input id="inputElvNEFinish" name="elv_ne_finish" type="text" value="<?php echo scrub_out($level->elv_ne_finish); ?>">
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elv_sw_start'); ?>">
  <label class="control-label" for="inputElvSWStart">SW Start</label>
  <div class="controls">
    <input id="inputElvSWStart" name="elv_sw_start" type="text" value="<?php echo scrub_out($level->elv_sw_start); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_sw_finish'); ?>">
  <label class="control-label" for="inputElvSWFinish">SW Finish</label>
  <div class="controls">
    <input id="inputElvSWFinish" name="elv_sw_finish" type="text" value="<?php echo scrub_out($level->elv_sw_finish); ?>">
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elv_se_start'); ?>">
  <label class="control-label" for="inputElvSEStart">SE Start</label>
  <div class="controls">
    <input id="inputElvSEStart" name="elv_se_start" type="text" value="<?php echo scrub_out($level->elv_se_start); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_se_finish'); ?>">
  <label class="control-label" for="inputElvSEFinish">SE Finish</label>
  <div class="controls">
    <input id="inputElvSEFinish" name="elv_se_finish" type="text" value="<?php echo scrub_out($level->elv_se_finish); ?>">
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elv_center_start'); ?>">
  <label class="control-label" for="inputElvCenterStart">Center Start</label>
  <div class="controls">
    <input id="inputElvCenterStart" name="elv_center_start" type="text" value="<?php echo scrub_out($level->elv_center_start); ?>">
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_center_finish'); ?>">
  <label class="control-label" for="inputElvCenterFinish">Center Finish</label>
  <div class="controls">
    <input id="inputElvCenterFinish" name="elv_center_finish" type="text" value="<?php echo scrub_out($level->elv_center_finish); ?>">
  </div>
</div>
<div class="control-group span10">
  <h4>Excavators</h4></hr />
</div>
<?php 
  // Current valid users
  $excavators = User::get('enabled'); 
?>
<div class="control-group span4<?php Error::display_class('excavator_one'); ?>">
  <label class="control-label" for="inputExcavatorone">First</label>
  <div class="controls">
    <select id="inputExcavatorone" name="excavator_one">
      <option value="">&nbsp;</option>
        <?php 
        foreach ($excavators as $user) { 
          $is_selected = '';
          if ($level->excavator_one == $user->uid) { $is_selected = ' selected="selected"'; }
        ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavator_two'); ?>">
  <label class="control-label" for="inputExcavatortwo">Second</label>
  <div class="controls">
    <select id="inputExcavatortwo" name="excavator_two">
      <option value="">&nbsp;</option>
      <?php
      foreach ($excavators as $user) { 
        $is_selected = '';
        if ($level->excavator_two == $user->uid) { $is_selected = ' selected="selected"'; }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('excavator_three'); ?>">
  <label class="control-label" for="inputExcavatorthree">Third</label>
  <div class="controls">
    <select id="inputExcavatorthree" name="excavator_three">
      <option value="">&nbsp;</option>
      <?php
      foreach ($excavators as $user) { 
        $is_selected = '';
        if ($level->excavator_three == $user->uid) { $is_selected = ' selected="selected"'; }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavator_four'); ?>">
  <label class="control-label" for="inputExcavatorfour">Fourth</label>
  <div class="controls">
    <select id="inputExcavatorfour" name="excavator_four">
      <option value="">&nbsp;</option>
      <?php 
      foreach ($excavators as $user) { 
        $is_selected = '';
        if ($level->excavator_four == $user->uid) { $is_selected = ' selected="selected"'; }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span10">
  <h4>Notes</h4><hr />
</div>
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">Describe: Sediment, Artifacts, Krotovina, Features </label>
  <div class="controls">
  	<textarea id="inputDescription" name="description" class="textbox" rows="5"><?php echo scrub_out($level->description); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('difference'); ?>">
  <label class="control-label" for="inputDifference">Describe the differences and similaraities compared to the last level</label>
  <div class="controls">
  	<textarea id="inputDifference" name="difference" class="textbox" rows="5"><?php echo scrub_out($level->difference); ?></textarea>
  </div>
</div>
<div class="control-group span8<?php Error::display_class('notes'); ?>">
  <label class="control-label" for="inputNotes">Did you find anything interesting or significant?</label>
  <div class="controls">
    <textarea id="inputNotes" name="notes" class="textbox" rows="5"><?php echo scrub_out($level->notes); ?></textarea>
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
    <button class="btn btn-primary" type="submit">Update</button>
  </div>
</div>
</form>
</fieldset>
<fieldset><legend>Upload</legend>
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/level/upload">
 	<input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
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
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Pictures</a></li>
  <li><a href="#3dmodel" data-toggle="tab">3D Models</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/level/images'); ?>
  </div> <!-- End Picture tab -->
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/level/3dmodel'); ?>
  </div> <!-- End 3dModel Tab -->
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/level/media'); ?>
  </div> <!-- End Media tab pane -->
</div> <!-- End Container -->
</fieldset>
