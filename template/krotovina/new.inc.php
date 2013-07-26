<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Krotovina - <?php echo \UI\sess::$user->site->name; ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_krotovina" method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/create">
<div class="control-group span4<?php Error::display_class('initial_rn'); ?>">
  <label class="control-label" for="inputInitialRN">Initial RN</label>
  <div class="controls">
    <input id="inputInitialRN" name="initial_rn" type="text" value="<?php echo scrub_out($_POST['initial_rn']); ?>" />
  </div>
</div>
<div class="control-group span8<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How does it differ from surrounding matrix</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description"><?php echo scrub_out($_POST['description']); ?></textarea>
  </div>
</div>
<div class="control-group span8<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Keywords</label>
  <div class="controls">
    <input id="inputKeywords" name="keywords" type="text" value="<?php echo scrub_out($_POST['keywords']); ?>" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('image'); ?>">
  <label class="control-label" for="inputUpload">Image</label>
  <div class="controls">
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
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
