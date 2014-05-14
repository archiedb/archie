<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$material_count = Stats::material_records('material',$material->uid); 
?>
<tr>
  <td>
    <?php echo scrub_out($material->name); ?>
  </td>
  <td><?php echo scrub_out($material_count['count']); ?></td>
	<td><?php echo \UI\boolean_word($material->enabled); ?></td>
  <td>
		<div class="btn-group">
      <?php if ($material->enabled) { ?>
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/material/disable/<?php echo scrub_out($material->uid); ?>">Disable</a>
      <?php } else { ?>
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/material/enable/<?php echo scrub_out($material->uid); ?>">Enable</a>
      <?php } ?>
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
       <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/manage/material/edit/<?php echo scrub_out($material->uid); ?>">Edit</a></li>
       </ul>
		</div>
  </td>
</tr>
