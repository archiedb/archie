<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/records/view/<?php echo scrub_out($record->uid); ?>">
      <?php echo scrub_out($record->record); ?></a>
  </td>
  <td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo scrub_out($record->level->unit->name); ?></td>
	<td><?php echo scrub_out($record->level->quad->name); ?></td>
	<td><?php echo \UI\record_link($record->level->uid,'level',$record->level->record); ?></td>
	<td><?php echo \UI\record_link($record->feature->uid,'feature',$record->feature->catalog_id); ?></td>
	<td><?php echo scrub_out($record->lsg_unit->name); ?></td>
	<td><?php echo \UI\search_link('material',$record->material->name); ?></td>
	<td><?php echo \UI\search_link('classification',$record->classification->name); ?></td>
  <td>
    <div class="btn-group pull-right">
      <button class="btn btn-info" data-toggle="collapse" data-target="#more_<?php echo scrub_out($record->uid); ?>_info">More</button>
      <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
    <ul class="dropdown-menu">
      <li><a target="_blank" href="<?php echo Config::get('web_path'); ?>/records/print/<?php echo scrub_out($record->uid); ?>/ticket">Print Ticket</a></li>
      <li><a href="<?php echo Config::get('web_path'); ?>/records/edit/<?php echo scrub_out($record->uid); ?>">Edit</a></li>
      <?php if (Access::has('record','delete')) { ?>
      <li><a href="#confirmdel_<?php echo scrub_out($record->uid); ?>" role="button" data-toggle="modal">Delete</a></li>
      <?php } ?>
    </ul>
    </div>
    <?php 
      if (Access::has('record','delete')) { 
        include \UI\template('/records/modal_delete_record'); 
      }
    ?>
  </td>
</tr> 
<tr style="border:0px;">
  <td colspan="10">
  <div class="panel-collapse collapse" id="more_<?php echo scrub_out($record->uid); ?>_info">
    <div class="panel-body">
      <em>Created by <?php echo scrub_out($record->user->username); ?> on <?php echo scrub_out(date('d-M-Y',$record->created)); ?>
      <?php if ($record->updated) { ?>last updated on <?php echo scrub_out(date('d-M-Y',$record->updated)); ?><?php } ?></em>
      <div class="panel panel-default">
        <div class="panel-heading">Notes</div>
        <div class="panel-body">
          <?php $record->_print('notes'); ?>
        </div>
      </div>
    </div>
  </div>
  </td>
</tr>
