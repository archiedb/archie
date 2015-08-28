<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
 <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>" class="btn btn-info">View Level</a>
  <?php if (!$level->closed) { ?>
  <a href="<?php echo Config::get('web_path'); ?>/level/checkclose/<?php echo scrub_out($level->uid); ?>" class="btn btn-danger">Close</a>
  <?php } else { ?>
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php echo scrub_out($level->uid) ?>" class="btn btn-success">Generate Report</a>
  <?php } ?>
</p>
<h3>Edit Level <?php echo scrub_out($level->record); ?></h3>
</div> <!-- End Header -->
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="update_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('unit'); ?>">
    <label class="col-md-2 control-label" for="inputUnit">Unit</label>
    <div class="col-md-2">
  	  <select name="unit" class="form-control">
    	<?php foreach (unit::$values as $value) { 
  	  	$is_selected = ''; 
    		if ($level->unit == $value) { $is_selected=" selected=\"selected\""; } 
    	?>
    		<option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option> 
    	<?php } ?>
      </select>
    </div>
    </div>
    <div class="<?php Error::form_class('quad'); ?>">
    <label class="col-md-2 control-label" for="inputQuad">Quad</label>
    <div class="col-md-2">
      <select id="inputQuad" name="quad" class="form-control">
        <?php foreach (quad::$values as $key=>$value) {
          $is_selected = '';
          if ($level->quad->uid == $key) { $is_selected=" selected=\"selected\""; }
        ?>
        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
       <?php } ?>
     </select>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel">Level</label>
    <div class="col-md-2">
    	<input class="form-control" id="inputLevel" name="catalog_id" type="text" value="<?php \UI\form_value(array('post'=>'catalog_id','var',$level->catalog_id)); ?>" />
    </div>
    </div> 
    <div class="<?php Error::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
    <div class="col-md-2">
    	<select name="lsg_unit" class="form-control">
    	<?php foreach (lsgunit::$values as $key=>$name) { 
    		$is_selected = ''; 
    		if ($level->lsg_unit->uid == $key) { $is_selected=" selected=\"selected=\""; }
    	?>
        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
    	<?php } ?>
  	  </select>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input class="form-controL" id="inputNorthing" type="text" name="northing" value="<?php \UI\form_value(array('post'=>'northing','var'=>$level->northing)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input class="form-control" id="inputEasting" type="text" name="easting" value="<?php \UI\form_value(array('post'=>'easting','var'=>$level->easting)); ?>">
    </div>
    </div>
  </div>
</div>
  <h4>Elevations</h4><hr />
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_nw_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvNWStart">NW Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvNWStart" name="elv_nw_start" type="text" value="<?php \UI\form_value(array('post'=>'elv_nw_start','var'=>$level->elv_nw_start)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('elv_nw_finish'); ?>">
    <label class="col-md-2 control-label" for="inputElvNWFinish">NW Finish</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvNWFinish" name="elv_nw_finish" type="text" value="<?php \UI\form_value(array('post'=>'elv_nw_finish','var'=>$level->elv_nw_finish)); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_ne_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvNEStart">NE Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvNEStart" name="elv_ne_start" type="text" value="<?php \UI\form_value(array('post'=>'elv_ne_start','var'=>$level->elv_ne_start)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('elv_ne_finish'); ?>">
    <label class="col-md-2 control-label" for="inputElvNEFinish">NE Finish</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvNEFinish" name="elv_ne_finish" type="text" value="<?php \UI\form_value(array('post'=>'elv_ne_finish','var'=>$level->elv_ne_finish)); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_sw_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvSWStart">SW Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvSWStart" name="elv_sw_start" type="text" value="<?php \UI\form_value(array('post'=>'elv_sw_start','var'=>$level->elv_sw_start)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('elv_sw_finish'); ?>">
    <label class="col-md-2 control-label" for="inputElvSWFinish">SW Finish</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvSWFinish" name="elv_sw_finish" type="text" value="<?php \UI\form_value(array('post'=>'elv_sw_finish','var'=>$level->elv_sw_finish)); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_se_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvSEStart">SE Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvSEStart" name="elv_se_start" type="text" value="<?php \UI\form_value(array('post'=>'elv_se_start','var'=>$level->elv_se_start)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('elv_se_finish'); ?>">
    <label class="col-md-2 control-label" for="inputElvSEFinish">SE Finish</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvSEFinish" name="elv_se_finish" type="text" value="<?php \UI\form_value(array('post'=>'elv_se_finish','var'=>$level->elv_se_finish)); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_center_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvCenterStart">Center Start</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvCenterStart" name="elv_center_start" type="text" value="<?php \UI\form_value(array('post'=>'elv_center_start','var'=>$level->elv_center_start)); ?>">
    </div>
    </div>
    <div class="<?php Error::form_class('elv_center_finish'); ?>">
    <label class="col-md-2 control-label" for="inputElvCenterFinish">Center Finish</label>
    <div class="col-md-2">
      <input class="form-control" id="inputElvCenterFinish" name="elv_center_finish" type="text" value="<?php \UI\form_value(array('post'=>'elv_center_finish','var'=>$level->elv_center_finish)); ?>">
    </div>
    </div>
  </div>
</div>
  <h4>Excavators</h4></hr />
<?php 
  // Current valid users
  if (Access::has('user','manage')) { 
    $excavators = User::get('all');
  } else {
    $excavators = User::get('enabled'); 
  }
?>
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('excavator_one'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatorone">First</label>
    <div class="col-md-2">
      <select class="form-control" id="inputExcavatorone" name="excavator_one">
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
    <div class="<?php Error::form_class('excavator_two'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatortwo">Second</label>
    <div class="col-md-2">
      <select class="form-control" id="inputExcavatortwo" name="excavator_two">
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
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('excavator_three'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatorthree">Third</label>
    <div class="col-md-2">
      <select id="inputExcavatorthree" name="excavator_three" class="form-control">
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
    <div class="<?php Error::form_class('excavator_four'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatorfour">Fourth</label>
    <div class="col-md-2">
      <select id="inputExcavatorfour" name="excavator_four" class="form-control">
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
  </div>
</div>
  <h4>Notes</h4><hr />
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('description'); ?>">
    <label class="col-md-2 control-label" for="inputDescription">Describe: Sediment, Artifacts, Krotovina, Features </label>
    <div class="col-md-6">
  	  <textarea id="inputDescription" name="description" class="form-control" rows="5"><?php \UI\form_value(array('post'=>'description','var'=>$level->description)); ?></textarea>
    </div>
    </div>
  </div><div class="form-group">
    <div class="<?php Error::form_class('difference'); ?>">
    <label class="col-md-2 control-label" for="inputDifference">Describe the differences and similarities compared to the last level</label>
    <div class="col-md-6">
    	<textarea id="inputDifference" name="difference" class="form-control" rows="5"><?php \UI\form_value(array('post'=>'difference','var'=>$level->difference)); ?></textarea>
    </div>
    </div>
  </div><div class="form-group">
    <div class="<?php Error::form_class('notes'); ?>">
    <label class="col-md-2 control-label" for="inputNotes">Did you find anything interesting or significant?</label>
    <div class="col-md-6">
      <textarea id="inputNotes" name="notes" class="form-control" rows="5"><?php \UI\form_value(array('post'=>'notes','var'=>$level->notes)); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="col-md-2 col-md-offset-2">
      <input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
      <button class="btn btn-primary" type="submit">Update</button>
    </div>
  </div>
</div>
</form>
<h4>Upload</h4><hr />
<form class="form-inline" enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/level/upload">
 	<input type="hidden" name="uid" value="<?php echo scrub_out($level->uid); ?>" />
  <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
<div class="row">
  <div class="form-group">
    <label class="col-md-2 control-label" for="inputDescription">Description</label>
    <div class="col-md-2">
      <input type="text" class="form-control" name="description" />
    </div>
    <div class="col-md-4 col-md-offset-2">
        <input type="file" name="media" class="filestyle" data-buttonText="" data-buttonbefore="true">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" type="submit">Upload</button>
    </div>
  </div>
</div>
</form>
<br />
<div class="row">
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
