<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
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
$items = $record->get_media(); 
foreach ($items as $item) {
?>
<tr>
  <td><?php echo basename($item['filename']); ?></td>
  <td><?php echo date('d-M-Y H:i:s',filemtime(Config::get('data_root') . '/' . $item['filename'])); ?></td>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/media/media/<?php echo scrub_out($item['uid']); ?>" class="btn btn-small">Download</a>
      <?php if (Access::has('media','delete',$item['uid'])) { ?>
      <a href="#" class="btn btn-small btn-danger">Delete</a>
      <?php } ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
