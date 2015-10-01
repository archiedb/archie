<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$items = Content::feature($feature->uid,'media'); 
if (count($items)) { 
?>
<table class="table table-hover">
<thead>
<tr>
  <th>Description</th>
  <th>Uploaded</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
foreach ($items as $item) {
  $media = new Content($item,'media','feature'); 
  $info = pathinfo($media->filename); 
  $extension = $info['extension']; 
  $name = strlen($media->notes) ? $media->notes : basename($media->filename); 
?>
<tr>
  <td><?php echo scrub_out($name); ?></td>
  <td><?php echo date('d-M-Y H:i:s',filemtime($media->filename)); ?></td>
  <td>
      <?php if (in_array($extension,array('stl'))) { ?>
      <a class="btn btn-small btn-info" href="<?php echo Config::get('web_path'); ?>/viewer/<?php echo scrub_out($extension); ?>/<?php echo scrub_out($media->uid); ?>">3d View</a>
      <?php } ?>
      <?php if (Access::has('media','download',$media->uid)) { ?>
      <a href="<?php echo Config::get('web_path'); ?>/media/media/<?php $media->_print('uid'); ?>" class="btn btn-primary btn-small">Download</a>
      <?php } ?>
      <?php if (Access::has('media','delete',$media->uid)) { ?>
      <button type="button" data-target="#confirm_delete_media_<?php $media->_print('uid'); ?>" data-toggle="modal" class="btn btn-small btn-danger">Delete</button>
      <?php require \UI\template('/feature/modal_delete_media'); ?>
      <?php } ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } else { ?>
<div class="row"><h3 class="text-center">No Media Items</h3></div>
<?php } ?>
