<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Level - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('unit'); ?>">
    <label class="col-md-2 control-label" for="inputUnit">Unit</label>
    <div class="col-md-2">
  	  <select id="inputUnit" class="form-control" name="unit">
      	<option value="-1">&nbsp;</option> 
  	    <?php foreach (unit::$values as $value) {
  	        $is_selected = '';
            if (isset($_POST['unit'])) { 
    	        if ($_POST['unit'] == $value) { $is_selected=" selected=\"selected\""; }
            } 
  	    ?>
  	    <option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
    	</select>
    </div>
    </div>
    <div class="<?php Error::form_class('quad'); ?>">
    <label class="col-md-2 control-label" for="inputQuad">Quad</label>
    <div class="col-md-2">
  	  <select id="inputQuad" class="form-control" name="quad"> 
        <?php foreach (quad::$values as $key=>$value) { 
          $is_selected = '';
          if (isset($_POST['quad'])) { 
            if ($_POST['quad'] == $key) { $is_selected=" selected=\"selected\""; }
          }
        ?>
        <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
        <?php } ?>
      </select>
    </div>
    </div>
  </div> <!-- GROUP END -->
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel">Level</label>
    <div class="col-md-2">
  	  <input id="inputLevel" class="form-control" name="catalog_id" type="text" value="<?php \UI\form_value('catalog_id'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit">L. U.</label>
    <div class="col-md-2">
  	  <select name="lsg_unit" class="form-control">
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
  </div> <!-- GROUP END -->
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input id="inputNorthing" class="form-control" name="northing" type="text" value="<?php \UI\form_value('northing'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input id="inputEasting" class="form-control" name="easting" type="text" value="<?php \UI\form_value('easting'); ?>" />
    </div>
    </div>
  </div>
</div>
<div class="col-md-12">
  <h5>Starting Elevations</h5>
  <hr />
</div>
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_nw_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvNWStart">NW</label>
    <div class="col-md-2">
      <input id="inputElvNWStart" class="form-control" name="elv_nw_start" type="text" value="<?php \UI\form_value('elv_nw_start'); ?>" />
    </div>
    </div>
    <div class="<?Php Error::form_class('elv_ne_start'); ?>"> 
    <label class="col-md-2 control-label" for="inputElvNEStart">NE</label>
    <div class="col-md-2">
      <input id="inputElvNEStart" class="form-control" name="elv_ne_start" type="text" value="<?php \UI\form_value('elv_ne_start'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_sw_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvSWStart">SW</label>
    <div class="col-md-2">
    <input id="inputElvSWStart" class="form-control" name="elv_sw_start" type="text" value="<?php \UI\form_value('elv_sw_start'); ?>" />
    </div>
    </div>
    <div class="<?php Error::form_class('elv_se_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvSEStart">SE</label>
    <div class="col-md-2">
      <input id="inputElvSEStart" class="form-control" name="elv_se_start" type="text" value="<?php \UI\form_value('elv_se_start'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('elv_center_start'); ?>">
    <label class="col-md-2 control-label" for="inputElvCenterStart">Center</label>
    <div class="col-md-2">
    <input id="inputElvCenterStart" class="form-control" name="elv_center_start" type="text" value="<?php \UI\form_value('elv_center_start'); ?>" />
    </div>
    </div>
  </div>
</div>
<div class="col-md-12">
  <h5>Excavators</h5>
  <hr />
</div>
<div class="row">
<?php 
  // Current valid users
  $excavators = User::get('enabled'); 
?>
  <div class="form-group">
    <div class="<?php Error::form_class('excavator_one'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatorone">First</label>
    <div class="col-md-2">
      <?php if (!Access::is_admin()) { $onedisabled='disabled="disabled"'; } ?>
      <select id="inputExcavatorone" class="form-control" name="excavator_one" <?php echo $onedisabled; ?>>
        <option value="">&nbsp;</option>
        <?php
        foreach ($excavators as $user) { 
          $is_selected = '';
          if (\UI\sess::$user->uid == $user->uid) { $is_selected = ' selected="selected"'; }
        ?>
        <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
        <?php } ?>
      </select>
    </div>
    </div>
    <div class="<?php Error::form_class('excavator_two'); ?>">
    <label class="col-md-2 control-label" for="inputExcavatortwo">Second</label>
    <div class="col-md-2">
      <select id="inputExcavatortwo" class="form-control" name="excavator_two">
        <option value="">&nbsp;</option>
        <?php
        foreach ($excavators as $user) { 
          $is_selected = '';
          if (isset($_POST['excavator_tow'])) {
            if ($_POST['excavator_two'] == $user->uid) { $is_selected = ' selected="selected"'; }
          }
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
          if (isset($_POST['excavator_three'])) {
            if ($_POST['excavator_three'] == $user->uid) { $is_selected = ' selected="selected"'; }
          }
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
          if (isset($_POST['excavator_four'])) {
            if ($_POST['excavator_four'] == $user->uid) { $is_selected = ' selected="selected"'; }
          }
        ?>
        <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
        <?php } ?>
      </select>
    </div>
    </div> 
  </div>
</div><div class="row">
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
