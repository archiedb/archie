<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$images = Content::record($record->uid,'image'); 
$i=0; 
?>
<div class="row">
<?php if (count($images) == 0) { ?>
  <h3 class="text-center">No Images</h3>
<?php } ?>
<?php
foreach ($images as $uid) {
  $image = new Content($uid,'image','record'); 
  if ($i/3 == floor($i/3)) {
      echo '</div><div class="row">';
  }
  $i++; 
?>
  <li class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <a href="<?php echo Config::get('web_path'); ?>/media/image/record/<?php $image->_print('uid'); ?>" target="_blank">
        <img src="<?php echo Config::get('web_path'); ?>/media/image/record/<?php $image->_print('uid'); ?>/thumb" alt="Image <?php echo $i; ?>" />
      </a>
      <div class="caption">
      <p>
        <?php $image->_print('notes'); ?>
      </p>
      <hr />
      <p class="text-center">
      <?php if (\UI\sess::location('action') != 'view') { ?>
      <?php if (Access::has('media','edit')) { ?>
        <button type="button" class="btn btn-primary btn-xs" data-target="#confirm_edit_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Edit</button>
      <?php } ?>
      <?php if (Access::has('media','delete')) { ?>
        <button class="btn btn-danger btn-xs" data-target="#confirm_delete_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Delete</button>
      <?php } ?>
      <?php 
      if (Access::has('media','delete')) { 
        require \UI\template('/records/modal_delete_image'); 
      } 
      if (Access::has('media','edit')) { 
        require \UI\template('/records/modal_edit_image'); 
      } 
      ?>
      <?php } ?>
      </p>
    </div>
  </div>
</div>
<?php } ?>
</div>
