<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$items = $record->get_media(); 
if (count($items)) { 
?>
<table class="table table-bordered table-hover">
<thead>
<tr>
  <th>Filename</th>
  <th>Uploaded</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
foreach ($items as $item) {
  $info = pathinfo($item['filename']); 
  $extension = $info['extension']; 
?>
<tr>
  <td><?php echo scrub_out($item['notes']); ?></td>
  <td><?php echo date('d-M-Y H:i:s',filemtime(Config::get('data_root') . '/' . $item['filename'])); ?></td>
  <td>
      <?php if (in_array($extension,array('stl'))) { ?>
      <a class="btn btn-small btn-info" href="<?php echo Config::get('web_path'); ?>/viewer/<?php echo scrub_out($extension); ?>/<?php echo scrub_out($item['uid']); ?>">3d View</a>
      <?php } ?>
      <a href="<?php echo Config::get('web_path'); ?>/media/media/<?php echo scrub_out($item['uid']); ?>" class="btn btn-small">Download</a>
      <?php if (Access::has('media','delete',$item['uid'])) { ?>
      <a href="#" class="btn btn-small btn-danger">Delete</a>
      <?php } ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>
