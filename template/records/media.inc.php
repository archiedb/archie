<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$items = Content::record($record->uid,'media'); 
if (count($items)) { 
?>
<table class="table table-bordered table-hover">
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
  $media = new Content($item,'media'); 
  $info = pathinfo($media->filename); 
  $extension = $info['extension']; 
  $name = strlen($media->notes) ? $media->notes : $media->filename;
?>
<tr>
  <td><?php echo scrub_out($name); ?></td>
  <td><?php echo date('d-M-Y H:i:s',filemtime($media->filename)); ?></td>
  <td>
      <?php if (in_array($extension,array('stl'))) { ?>
      <a class="btn btn-small btn-info" href="<?php echo Config::get('web_path'); ?>/viewer/<?php echo scrub_out($extension); ?>/<?php echo scrub_out($media->uid); ?>">3d View</a>
      <?php } ?>
      <a href="<?php echo Config::get('web_path'); ?>/media/media/<?php echo scrub_out($media->uid); ?>" class="btn btn-small">Download</a>
      <?php if (Access::has('media','delete',$media->uid)) { ?>
      <a href="#confirm_delete_media_<?php echo scrub_out($media->uid); ?>" role="button" data-toggle="modal" class="btn btn-small btn-danger">Delete</a>
      <?php require \UI\template('/records/modal_delete_media'); ?>
      <?php } ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>
