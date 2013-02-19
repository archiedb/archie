<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/new.php?action=edit&record_id=<?php echo scrub_out($record->uid); ?>">
      <?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></a>
  </td>
  <td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo scrub_out($record->unit); ?></td>
	<td><?php echo scrub_out(quad::$values[$record->quad]); ?></td>
	<td><?php echo scrub_out($record->level); ?></td>
	<td><?php echo scrub_out($record->feature); ?></td>
	<td><?php echo scrub_out(lsgunit::$values[$record->lsg_unit]); ?></td>
	<td><?php echo scrub_out($record->material->name); ?></td>
	<td><?php echo scrub_out($record->classification->name); ?></td>
  <td>
    <div class="btn-group">
      <button class="btn" data-toggle="collapse" data-target="#more_<?php echo scrub_out($record->uid); ?>_info">More</button>
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
    <ul class="dropdown-menu">
      <li><a href="<?php echo Config::get('web_path'); ?>/new.php?action=edit&record_id=<?php echo scrub_out($record->uid); ?>">Edit</a></li>
      <?php if ($GLOBALS['user']->access == '100') { ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=confirm_delete&record_id=<?php echo scrub_out($record->uid); ?>">Delete</a></li>
      <?php } ?>
    </ul>
    </div>
  </td>
</tr> 
<tr style="border:0px;">
  <td colspan="10">
  <div class="accordion-body collapse" style="height: 0px;" id="more_<?php echo scrub_out($record->uid); ?>_info">
    <div class="accordion-inner">
      <strong>Created by -</strong> <?php echo scrub_out($record->user->username); ?> on <?php echo scrub_out(date("m/d/y",$record->created)); ?>
      <blockquote><?php echo scrub_out($record->notes); ?></blockquote>
    </div>
  </div>
  </td>
</tr>
