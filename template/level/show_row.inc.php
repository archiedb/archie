<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
	<td><?php $level->unit->_print('name'); ?></td>
	<td><?php $level->quad->_print('name'); ?></td>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php $level->_print('uid'); ?>"><?php $level->_print('record'); ?></a>
  </td>
  <td><?php echo scrub_out($level->lsg_unit->name); ?></td>
  <td><?php echo \UI\boolean_word($level->closed); ?></td>
  <td>
    <div class="btn-group pull-right">
      <button class="btn btn-info" data-toggle="collapse" data-target="#more_<?php $level->_print('uid'); ?>_info">More</button>
      <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
    <ul class="dropdown-menu">
      <?php if (Access::has('admin','admin',$level->uid) OR !$level->closed) { ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/level/edit/<?php $level->_print('uid'); ?>">Edit</a></li>
      <?php } ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/records/search/level/<?php $level->_print('uid'); ?>">Records</a></li>
      <?php if (Access::has('level','delete',$level->uid)) { ?>
      <li><a href="#confirmdel_<?php $level->_print('uid'); ?>" role="button" data-toggle="modal">Delete</a></li>
      <?php } ?>
      <li><a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php $level->_print('uid'); ?>">Generate Report</a></li>
    </ul>
    </div>
    <?php 
      if (Access::has('level','delete',$level->uid)) { 
        include \UI\template('/level/modal_delete_confirm'); 
      }
    ?>
  </td>
</tr> 
<tr style="border:0px;">
  <td colspan="10">
  <div class="panel-collapse collapse" id="more_<?php echo scrub_out($level->uid); ?>_info">
    <div class="panel-body">
        <small>Created by <?php echo scrub_out($level->user->name); ?> on <?php echo scrub_out(date('d-M-Y',$level->created)); ?>
        <?php if ($level->updated) { ?>last updated on <?php echo scrub_out(date('d-M-Y',$level->updated)); ?><?php } ?></small>

        <div class="panel panel-default">
          <div class="panel-heading">
            Excavators
          </div>
          <div class="panel-body">
        <?php 
          $message = '';
          foreach (array('excavator_one','excavator_two','excavator_three','excavator_four') as $ex_check) {
            if ($level->$ex_check) { 
              $ex = new User($level->$ex_check);
              $message .= '<div class="col-md-2">' . \UI\record_link($level->$ex_check,'user',$ex->name) . '</div>';
            }
          }
          echo $message;
        ?>
          </div>
        </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          Describe: Sediment, Artifacts, Krotovina, Features
        </div>
        <div class="panel-body">
          <?php $level->_print('description'); ?>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          Describe the differences and similaraities compared to the last level.
        </div>
        <div class="panel-body">
          <?php $level->_print('differences'); ?>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          Did you find anything interesting or significant?
        </div>
        <div class="panel-body">
          <?php $level->_print('notes'); ?>
        </div>
      </div>
    </div>
  </div>
  </td>
</tr>
