<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<ul class="thumbnails">
<?php
$images = Content::level($level->uid,'image'); 
$i=0; 
foreach ($images as $uid) {
  $image = new Content($uid,'image'); 
  if ($i/4 == floor($i/4)) {
      echo '</ul><ul class="thumbnails">';
  }
  $i++; 
?>
  <li class="span3">
    <div class="thumbnail">
      <p class="text-center">
      <a href="<?php echo Config::get('web_path'); ?>/media/image/<?php echo scrub_out($image->uid); ?>" target="_blank"><img src="<?php echo Config::get('web_path'); ?>/media/image/<?php echo scrub_out($image->uid);?>/thumb" alt="Image <?php echo $i; ?>" /></a><br />
        <?php echo scrub_out($image->notes); ?>
      </p>
      <hr />
      <p class="text-center">
      <?php if (\UI\sess::location('action') != 'view') { ?>
      <?php if (Access::has('media','write',$image->uid)) { ?>
        <a class="btn btn-small" href="#confirm_edit_image_<?php echo scrub_out($image->uid); ?>" role="button" data-toggle="modal">Edit</a>
      <?php } ?>
      <?php if (Access::has('media','delete',$image->uid)) { ?>
        <a class="btn btn-danger btn-small" href="#confirm_delete_image_<?php echo scrub_out($image->uid); ?>" role="button" data-toggle="modal">Delete</a>
      <?php } ?>
      <?php 
      if (Access::has('media','delete',$image->uid)) { 
        require \UI\template('/level/modal_delete_image'); 
      } 
      if (Access::has('media','write',$image->uid)) { 
        require \UI\template('/level/modal_edit_image'); 
      } 
      ?>
      <?php } ?>
      </p>
    </div>
  </li>
<?php } ?>
</ul>
