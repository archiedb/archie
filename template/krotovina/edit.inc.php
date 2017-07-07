<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>Edit <?php echo scrub_out(\UI\sess::$user->site->name); ?> Krotovina - <?php echo scrub_out($krotovina->record); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_krotovina" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/update">
<div class="control-group span4<?php Err::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How is the krotovina differentiated from the surrounding sediments? What are its defining characteristics</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description"><?php echo scrub_out($krotovina->description); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Err::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Additional Notes?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords" ><?php echo scrub_out($krotovina->keywords); ?></textarea>
  </div>
</div>
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('level'); ?>">
    <label class="col-md-2 control-label" for="inputLevel"><abbr title="Unit:Quad:Level">Locus</abbr></label>
    <div class="col-md-2">
      <?php
        $user_levels = Level::get_open_user_levels();
        if (in_array($krotovina->level->uid,$user_levels) OR Access::has('record','admin')) {
          // For Record admins add the current one even if it's not open
          if (!in_array($krotovina->level->uid,$user_levels)) { $user_levels[] = $krotovina->level->uid; }
        ?>
      <select class="form-control" id="inputLevel" name="level">
        <option value="">No Level</option>
      <?php
        foreach ($user_levels as $level_uid) {
          $level = new Level($level_uid);
          $is_selected = '';
          if ($krotovina->level->uid == $level_uid) { $is_selected=' selected="selected="'; }
      ?>
        <option value="<?php echo scrub_out($level_uid); ?>"<?php echo $is_selected; ?>><?php $krotovina->_print('name'); ?></option>
      <?php } ?>
      </select>
      <?php } else { ?>
       <input id="levelText" type="text" name="textvalue" value="<?php $krotovina->level->_print('name'); ?>" disabled="disabled">
       <input id="inputLevel" name="level" type="hidden" value="<?php $krotovina->level->_print('uid'); ?>" />
      <?php } ?>
    </div>
    </div>
</div><div class="row">
    <div class="<?php Err::form_class('lsg_unit'); ?>">
    <label class="col-md-2 control-label" for="inputLsgUnit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></label>
    <div class="col-md-2">
      <select class="form-control" name="lsg_unit">
      <?php if (!lsgunit::is_valid($krotovina->lsg_unit->name)) { ?>
        <option value="<?php $krotovina->lsg_unit->_print('name'); ?>"><?php $krotovina->lsg_unit->_print('name'); ?></option>
      <?php } ?>
      <?php foreach (lsgunit::get_values() as $name) {
        $is_selected = '';
        if ($record->lsg_unit->name == $name) { $is_selected=" selected=\"selected=\""; }
      ?>
        <option value="<?php echo scrub_out($name); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
      <?php } ?>
      </select>
    </div>
    </div>
</div>
<div class="control-group span8">
  <div class="controls">
    <input type="hidden" name="krotovina_id" value="<?php echo scrub_out($krotovina->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
  </div>
</div>
</form>
<hr />
<h4>Upload</h4>
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/upload">
  <input type="hidden" name="krotovina_id" value="<?php $krotovina->_print('uid'); ?>" />
  <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
<div class="row">
  <div class="form-group">
    <label class="col-md-2 control-label" for="inputDescription">Description</label>
    <div class="col-md-4">
      <input type="text" class="form-control" name="description" />
    </div>
    <div class="col-md-4">
      <input type="file" name="media" class="filestyle" data-buttonText="" data-buttonbefore="true">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" type="submit">Upload</button>
    </div>
  </div>
<div>
</form>
<hr />
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Images</a></li>
  <li><a href="#3dmodel" data-toggle="tab">3D Models</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/krotovina/images'); ?>
  </div>
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/krotovina/3dmodel'); ?>
  </div>
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/krotovina/media'); ?>
  </div>
</div>
