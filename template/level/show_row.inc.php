<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
	<td><?php echo scrub_out($level->unit); ?></td>
	<td><?php echo scrub_out($level->quad->name); ?></td>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>">
      <?php $level->_print('record'); ?></a>
  </td>
  <td><?php echo scrub_out($level->lsg_unit->name); ?></td>
  <td><?php echo \UI\boolean_word($level->closed); ?></td>
  <td>
    <div class="btn-group">
      <button class="btn" data-toggle="collapse" data-target="#more_<?php echo scrub_out($level->uid); ?>_info">More</button>
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
    <ul class="dropdown-menu">
      <?php if (Access::has('admin','admin',$level->uid) OR !$level->closed) { ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/level/edit/<?php echo scrub_out($level->uid); ?>">Edit</a></li>
      <?php } ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/record/search/level/<?php echo scrub_out($level->uid); ?>">Records</a></li>
      <?php if (Access::has('level','delete',$level->uid)) { ?>
      <li><a href="#confirmdel_<?php echo scrub_out($level->uid); ?>" role="button" data-toggle="modal">Delete</a></li>
      <?php } ?>
      <li><a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php echo scrub_out($level->uid); ?>">Generate Report</a></li>
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
  <div class="accordion-body collapse" style="height: 0px;" id="more_<?php echo scrub_out($level->uid); ?>_info">
    <div class="accordion-inner">
      <strong>Created by -</strong> <?php echo scrub_out($level->user->name); ?> on <?php echo scrub_out(date('d-M-Y',$level->created)); ?>
      <?php if ($level->updated) { ?>last updated on <?php echo scrub_out(date('d-M-Y',$level->updated)); ?><?php } ?>
        <table class="table table-hover table-bordered table-white">
        <tr>
          <th>NORTHING</th><td><?php echo scrub_out($level->northing); ?></td>
          <th>EASTING</th><td><?php echo scrub_out($level->easting); ?></td>
        </tr>
        <tr>
          <th>NW Start</th><td><?php echo scrub_out($level->elv_nw_start); ?></td>
          <th>NW Finish</th><td><?php echo scrub_out($level->elv_nw_finish); ?></td>
        </tr>
        <tr>
          <th>NE Start</th><td><?php echo scrub_out($level->elv_ne_start); ?></td>
          <th>NE Finish</th><td><?php echo scrub_out($level->elv_ne_finish); ?></td>
        </tr>
        <tr>
          <th>SW Start</th><td><?php echo scrub_out($level->elv_sw_start); ?></td>
          <th>SW Finish</th><td><?php echo scrub_out($level->elv_sw_finish); ?></td>
        </tr>
        <tr>
          <th>SE Start</th><td><?php echo scrub_out($level->elv_se_start); ?></td>
          <th>SE Finish</th><td><?php echo scrub_out($level->elv_se_finish); ?></td>
        </tr>
        <tr>
          <th>Center Start</th><td><?php echo scrub_out($level->elv_center_start); ?></td>
          <th>Center Finish</th><td><?php echo scrub_out($level->elv_center_finish); ?></td>
        </tr>
      </table>
      <h5>Notes</h5>
        <table class="table table-hover table-bordered table-white">
          <tr>  
            <th>Describe: Sediment, Artifacts, Krotovina, Features</th>
        </tr>
        <tr>
          <td><?php echo scrub_out($level->description); ?></td>
        </tr>
        <tr>
          <th>Describe the differences and similaraities compared to the last level.</th>
        </tr>
        <tr>
          <td><?php echo scrub_out($level->difference); ?></td>
        </tr>
        <tr>
          <th>Did you find anything interesting or significant?</th>
        </tr>
        <tr>
          <td><?php echo scrub_out($level->notes); ?></td>
        </tr>
        </table>
    </div>
  </div>
  </td>
</tr>
