<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <p class="pull-right">
    <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/feature/<?php echo scrub_out($feature->catalog_id); ?>">View Records</a>
  </p>
  <h3>Edit <?php echo scrub_out(\UI\sess::$user->site->name); ?> Feature - <?php $feature->_print('record'); ?></h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_feature" method="post" action="<?php echo Config::get('web_path'); ?>/feature/update">
<div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('description'); ?>">
    <label class="control-label" for="inputDescription">How is the feature differentiated from the surrounding sediments? What are its defining characteristics?</label><br />
    <div class="col-md-10">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="description"><?php \UI\form_value(array('post'=>'description','var'=>$feature->description)); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Error::form_class('keywords'); ?>">
    <label class="control-label" for="inputKeywords">Additional Notes?</label><br />
    <div class="col-md-10">
      <textarea class="form-control" placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords"><?php echo scrub_out($feature->keywords); ?></textarea>
    </div>
    </div>
  </div>
</div><div class="row">
<div class="form-group">
    <input type="hidden" name="feature_id" value="<?php echo scrub_out($feature->uid); ?>" />
  	<input type="submit" class="btn btn-primary" value="Update" />
</div>
</form>
<h4>Upload</h4><hr />
<form enctype="multipart/form-data" method="post" action="<?php echo Config::get('web_path'); ?>/feature/upload">
  <input type="hidden" name="feature_id" value="<?php $feature->_print('uid'); ?>" />
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
    <?php require_once \UI\template('/feature/images'); ?>
  </div>
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/feature/3dmodel'); ?>
  </div>
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/feature/media'); ?>
  </div>
</div>
