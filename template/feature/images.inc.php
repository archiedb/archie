<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$images = Content::feature($feature->uid,'image'); 
$i=0; 
?>
<div class="row">
<?php if (count($images) == 0) { ?>
  <h3 class="text-center">No Images</h3>
<?php } ?>
<?php
foreach ($images as $uid) {
  $image = new Content($uid,'image','feature'); 
  if ($i/3 == floor($i/3)) {
      echo '</div><div class="row">';
  }
  $i++; 
?>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <a href="<?php echo Config::get('web_path'); ?>/media/image/feature/<?php $image->_print('uid'); ?>" target="_blank">
        <img src="<?php echo Config::get('web_path'); ?>/media/image/feature/<?php $image->_print('uid'); ?>/thumb" alt="Image <?php echo $i; ?>" />
      </a>
      <div class="caption">
      <?php if ($feature->image == $image->uid) { ?>
      <p class="text-center">
        <span class="label label-success">Primary Image</span>
      </p>
      <?php } else { ?>
      <p><br /></p>
      <?php } ?>
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
        <button type="button" class="btn btn-danger btn-xs" data-target="#confirm_delete_image_<?php echo scrub_out($image->uid); ?>" data-toggle="modal">Delete</button>
      <?php } ?>
        <button type="button" class="btn btn-success btn-xs" data-target="#confirm_primary_image_<?php echo $image->_print('uid'); ?>" data-toggle="modal">Primary</button>
      <?php 
      if (Access::has('media','delete')) { 
        require \UI\template('/feature/modal_delete_image'); 
      } 
      if (Access::has('media','edit')) { 
        require \UI\template('/feature/modal_edit_image'); 
      } 
      require \UI\template('/feature/modal_primary_image');
      ?>
      <?php } ?>
      </p>
      </div>
    </div>
  </div>
<?php } ?>
</div> <!-- Row -->
