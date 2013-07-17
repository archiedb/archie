<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/level/edit/<?php echo scrub_out($level->uid); ?>" class="btn">Edit Level</a>
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php echo scrub_out($level->uid) ?>" class="btn btn-success">Generate Report</a>
</p>
<h3><?php echo $level->site . '-' . $level->record; ?>
  <small>Entered by <?php echo $level->user->username; ?> on <?php echo date("d-M-Y H:i:s",$level->created); ?></small>
</h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>UNIT</th><td><?php echo scrub_out($level->unit); ?></td>
  <th>QUAD</th><td><?php echo scrub_out($level->quad); ?></td>
  <th>L.U</th><td><?php echo scrub_out($level->lsg_unit); ?></td>
</tr>
<tr>
  <th colspan="2">NORTHING</th><td><?php echo scrub_out($level->northing); ?></td>
  <th colspan="2">EASTING</th><td><?php echo scrub_out($level->easting); ?></td>
</tr>
</table>
<h5>Elevations<h5><hr />
<table class="table table-hover table-bordered table-white">
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
<h5>Excavators<h5><hr />
<?php 
  // Setup the users
  $ex_one = new User($level->excavator_one); 
  $ex_two = new User($level->excavator_two); 
  $ex_three = new User($level->excavator_three);
  $ex_four  = new User($level->excavator_four);
?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>First</th><td><?php echo scrub_out($ex_one->name); ?></td>
  <th>Second</th><td><?php echo scrub_out($ex_two->name); ?></td>
</tr>
<tr>
  <th>Third</th><td><?php echo scrub_out($ex_three->name); ?></td>
  <th>Fourth</th><td><?php echo scrub_out($ex_four->name); ?></td>
</tr>
</table>

