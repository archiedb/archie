<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <h4>New Feature - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_feature" method="post" action="<?php echo Config::get('web_path'); ?>/feature/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('description'); ?>">
    <label class="control-label" for="inputDescription">How is the feature differentiated from the surrounding sediments? What are its defining characteristics?</label><br />
    <div class="col-md-6">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="description" id="inputDescription" tabindex="1"><?php \UI\form_value('description'); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('keywords'); ?>">
    <label class="control-label" for="inputKeywords">Additional Notes?</label><br />
    <div class="col-md-6">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords" tabindex="2"><?php \UI\form_value('keywords'); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('initial_rn'); ?>">
    <label class="col-md-2 control-label" for="inputInitialRN">Station Index (RN)</label>
    <div class="col-md-2">
      <input placeholder="Initial Station Index" class="form-control" id="inputInitialRN" name="initial_rn" type="text" tabindex="3" value="<?php \UI\form_value('initial_rn'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('northing'); ?>">
    <label class="col-md-2 control-label" for="inputNorthing">Northing</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" tabindex="4" id="inputNorthing" name="northing" type="text" value="<?php \UI\form_value('northing'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('easting'); ?>">
    <label class="col-md-2 control-label" for="inputEasting">Easting</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" id="inputEasting" tabindex="5" name="easting" type="text" value="<?php \UI\form_value('easting'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('elevation'); ?>">
    <label class="col-md-2 control-label" for="inputElevation">Elevation</label>
    <div class="col-md-2">
      <input placeholder="0.000" class="form-control" tabindex="5" id="inputElevation" name="elevation" type="text" value="<?php \UI\form_value('elevation'); ?>" />
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel">Level</label>
    <div class="col-md-2">
      <?php
        $user_levels = Level::get_open_user_levels();
      ?>
      <select id="inputLevel" class="form-control" name="level">
        <option value="">No Level</option>
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
    </div> <!-- ERROR -->
  </div>
  <div class="form-group">
    <div class="<?php Err::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit">LSG Unit</label>
    <div class="col-md-2">
      <select class="form-control" name="lsg_unit">
        <?php foreach (lsgunit::get_values() as $name) {
          $is_selected = '';
          if (isset($_POST['lsg_unit'])) {
            if ($_POST['lsg_unit'] == $name) { $is_selected=" selected=\"selected=\""; }
          }
        ?>
        <option value="<?php echo scrub_out($name); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
      <?php } ?>
        </select>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="col-md-12">
  	<input type="submit" tabindex="6" class="btn btn-primary" value="Create" />
    </div>
  </div>
</div>
</form>
