<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$images = Content::level($level->uid,'3dmodel'); 
$i=0; 
?>
<div class="row">
<?php if (count($images) == 0) { ?>
  <h3 class="text-center">No 3d Models</h3>
<?php } ?>
<?php
foreach ($images as $uid) {
  $model = new Content($uid,'3dmodel','level'); 
  if ($i/3 == floor($i/3)) {
      echo '</div><div class="row">';
  }
  $info = pathinfo($model->filename);
  $extension = $info['extension'];
  $name = strlen($model->notes) ? $model->notes : basename($model->filename);
  $i++; 
?>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <?php if ($extension == 'stl') { ?>
      <a href="<?php echo Config::get('web_path'); ?>/viewer/stl/level/<?php echo scrub_out($model->uid); ?>" title="3D View"><img src="<?php echo Config::get('web_path'); ?>/media/3dmodel/level/<?php echo scrub_out($model->uid);?>/thumb" /></a>
      <?php } else { ?>
      <img src="<?php echo Config::get('web_path'); ?>/media/3dmodel/level/<?php echo scrub_out($model->uid);?>/thumb" />
      <?php } ?>
      <div class="caption">
        <p>
          <?php echo scrub_out($name); ?>
        </p>
        <p class="text-center">
      <?php if (\UI\sess::location('action') != 'view') { ?>
      <?php if (Access::has('media','read',$model->uid)) { ?>
          <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/media/3dmodel/<?php echo scrub_out($model->uid); ?>" title="Download">Download</a>
      <?php } ?>
      <?php if (Access::has('media','write',$model->uid)) { ?>
          <button type="button" class="btn btn-primary btn-small" data-target="#confirm_edit_3dmodel_<?php echo scrub_out($model->uid); ?>" data-toggle="modal">Edit</button>
      <?php } ?>
      <?php if (Access::has('media','delete',$model->uid)) { ?>
          <button type="button" class="btn btn-danger btn-small" data-target="#confirm_delete_3dmodel_<?php echo scrub_out($model->uid); ?>" data-toggle="modal">Delete</button>
      <?php } ?>
      <?php 
      if (Access::has('media','delete',$model->uid)) { 
        require \UI\template('/level/modal_delete_3dmodel'); 
      } 
      if (Access::has('media','write',$model->uid)) { 
        require \UI\template('/level/modal_edit_3dmodel'); 
      } 
      ?>
      <?php } ?>
      </p>
      </div>
    </div>
  </div>
<?php } ?>
</div>
