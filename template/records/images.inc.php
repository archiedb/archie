<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<ul class="thumbnails">
<?php
$images = $record->get_images();
foreach ($images as $image) {
  if ($i/4 == floor($i/4)) {
      echo '</ul><ul class="thumbnails">';
  }
  $i++; 
?>
  <li class="span3">
    <div class="thumbnail">
      <img src="<?php echo Config::get('web_path'); ?>/media/thumb/<?php echo scrub_out($image['uid']);?>" alt="Image <?php echo $i; ?>" />
      <hr />
      <p class="text-center">
        <?php echo $image['notes']; ?>
      </p>
      <p class="text-center">
        <a class="btn btn-small" target="_blank" href="<?php echo Config::get('web_path'); ?>/media/record/<?php echo scrub_out($image['uid']); ?>">Open</a>
      <?php if (Access::has('image','write',$image['uid'])) { ?>
        <a class="btn btn-small" href="#confirm_edit_image_<?php echo scrub_out($image['uid']); ?>" role="button" data-toggle="modal">Edit</a>
      <?php } ?>
      <?php if (Access::has('image','delete',$image['uid'])) { ?>
        <a class="btn btn-danger btn-small" href="#confirm_delete_image_<?php echo scrub_out($image['uid']); ?>" role="button" data-toggle="modal">Delete</a>
      <?php } ?>
      <?php 
      if (Access::has('image','delete',$image['uid'])) { 
        require \UI\template('/records/modal_delete_image'); 
      } 
      if (Access::has('image','write',$image['uid'])) { 
        require \UI\template('/records/modal_edit_image'); 
      } 
      ?>
      </p>
    </div>
  </li>
<?php } ?>
</ul>
