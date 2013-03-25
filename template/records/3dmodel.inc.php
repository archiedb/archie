<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<ul class="thumbnails">
<?php
$images = Content::record($record->uid,'3dmodel'); 
$i=0; 
foreach ($images as $uid) {
  $model = new Content($uid,'3dmodel'); 
  if ($i/4 == floor($i/4)) {
      echo '</ul><ul class="thumbnails">';
  }
  $info = pathinfo($model->filename);
  $extension = $info['extension'];
  $name = strlen($model->notes) ? $model->notes : basename($model->filename);
  $i++; 
?>
  <li class="span3">
    <div class="thumbnail">
      <p class="text-center">
      <?php if ($extension == 'stl') { ?>
      <a href="<?php echo Config::get('web_path'); ?>/viewer/stl/<?php echo scrub_out($model->uid); ?>" title="3D View"><img src="<?php echo Config::get('web_path'); ?>/media/3dmodel/<?php echo scrub_out($model->uid);?>/thumb" /></a>
      <?php } else { ?>
      <img src="<?php echo Config::get('web_path'); ?>/media/3dmodel/<?php echo scrub_out($model->uid);?>/thumb" />
      <?php } ?>
      <br />
      <?php echo scrub_out($name); ?>
      </p>
      <hr />
      <p class="text-center">
      <?php if (\UI\sess::location('action') != 'view') { ?>
      <?php if (Access::has('media','delete',$model->uid)) { ?>
        <a class="btn btn-small" href="<?php echo Config::get('web_path'); ?>/media/3dmodel/<?php echo scrub_out($model->uid); ?>" title="Download">Download</a>
      <?php } ?>
      <?php if (Access::has('media','write',$model->uid)) { ?>
        <a class="btn btn-small" href="#confirm_edit_3dmodel_<?php echo scrub_out($model->uid); ?>" role="button" data-toggle="modal">Edit</a>
      <?php } ?>
      <?php if (Access::has('media','delete',$model->uid)) { ?>
        <a class="btn btn-danger btn-small" href="#confirm_delete_3dmodel_<?php echo scrub_out($model->uid); ?>" role="button" data-toggle="modal">Delete</a>
      <?php } ?>
      <?php 
      if (Access::has('media','delete',$model->uid)) { 
        require \UI\template('/records/modal_delete_3dmodel'); 
      } 
      if (Access::has('media','write',$model->uid)) { 
        require \UI\template('/records/modal_edit_3dmodel'); 
      } 
      ?>
      <?php } ?>
      </p>
    </div>
  </li>
<?php } ?>
</ul>
