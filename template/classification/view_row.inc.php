<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$classification_count = Stats::classification_records('classification',$classification->uid); 
?>
<tr>
  <td>
    <?php echo scrub_out($classification->name); ?>
  </td>
  <td><?php echo scrub_out($classification_count['count']); ?></td>
	<td><?php echo \UI\boolean_word($classification->enabled); ?></td>
  <td>
		<div class="btn-group pull-right">
      <?php if ($classification->enabled) { ?>
      <a class="btn btn-danger" href="<?php echo Config::get('web_path'); ?>/manage/classification/disable/<?php echo scrub_out($classification->uid); ?>">Disable</a>
      <?php } else { ?>
      <a class="btn btn-success" href="<?php echo Config::get('web_path'); ?>/manage/classification/enable/<?php echo scrub_out($classification->uid); ?>">Enable</a>
      <?php } ?>
		</div>
  </td>
</tr>
