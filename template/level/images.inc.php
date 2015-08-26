<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }

$images = Content::level($level->uid,'image'); 
$i=0; 
?>
<div class="row">
<?php if (count($images) == 0) { ?>
  <h3 class="text-center">No Images</h3>
<?php } ?>
<?php
foreach ($images as $uid) {
  $image = new Content($uid,'image','level'); 
  if ($i/3 == floor($i/3)) {
      echo '</div><div class="row">';
  }
  $i++; 
?>
<div class="col-sm-6 col-md-4">
  <div class="thumbnail">
    <?php if ($level->image == $image->uid) { ?><span class="label label-success"><?php } ?>
    <a href="<?php echo Config::get('web_path'); ?>/media/image/level/<?php echo scrub_out($image->uid); ?>" target="_blank"><img src="<?php echo Config::get('web_path'); ?>/media/image/level/<?php echo scrub_out($image->uid);?>/thumb" alt="Image <?php echo $i; ?>" /></a>
    <div class="caption">
        <p>
        <?php echo scrub_out($image->notes); ?>
        </p>
        <p class="text-center">
      <?php if (\UI\sess::location('action') != 'view') { ?>
      <?php if (Access::has('media','write',$image->uid)) { ?>
        <button type="button" class="btn btn-primary btn-small" data-target="#confirm_edit_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Edit</button>
      <?php } ?>
      <?php if (Access::has('media','delete',$image->uid)) { ?>
        <button type="button" class="btn btn-danger btn-small" data-target="#confirm_delete_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Delete</button>
      <?php } ?>
        <button type="button" class="btn btn-success btn-small" data-target="#confirm_primary_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Primary</button>
      <?php 
      if (Access::has('media','delete',$image->uid)) { 
        require \UI\template('/level/modal_delete_image'); 
      } 
      if (Access::has('media','write',$image->uid)) { 
        require \UI\template('/level/modal_edit_image'); 
      } 
      require \UI\template('/level/modal_primary_image'); 
      ?>
      <?php } ?>
      </p>
    </div>
  </div>
</div>
<?php } ?>
</div>
<hr />
