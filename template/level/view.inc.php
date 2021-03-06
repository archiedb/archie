<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/level/<?php echo scrub_out($level->uid); ?>">View Records</a>
  <?php if (Access::has('level','reopen') AND $level->closed) { ?>
  <button type="button" data-target="#confirm_open_level_<?php echo scrub_out($level->uid); ?>" class="btn btn-danger" data-toggle="modal">Re-Open</button>
  <?php } ?>
  <?php if (Access::has('level','edit')) { ?>
  <a href="<?php echo Config::get('web_path'); ?>/level/edit/<?php echo scrub_out($level->uid); ?>" class="btn btn-primary">Edit</a>
  <?php } ?>
  <?php if (!$level->closed) { ?>
  <a href="<?php echo Config::get('web_path'); ?>/level/checkclose/<?php echo scrub_out($level->uid); ?>" class="btn btn-danger">Close</a>
  <?php } else { ?>
  <a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php echo scrub_out($level->uid) ?>" class="btn btn-success">Report</a>
  <?php } ?>
</p>
<?php if (Access::has('level','reopen') AND $level->closed) { ?>
  <?php include \UI\template('/level/modal_open_confirm'); ?>
<?php } ?>
<h3>Level: <?php echo scrub_out($level->site->name); ?><?php echo scrub_out($level->record); ?>
  <?php if ($level->closed) { ?><span class="label label-important">CLOSED</span><?php } ?>
  <br />
  <small><em>entered by <?php echo $level->user->username; ?> on <?php echo date("d-M-Y H:i:s",$level->created); ?></em></small>
</h3>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<?php Event::display('warnings'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Unit</th><td><?php echo scrub_out($level->unit->name); ?></td>
  <th>Quad</th><td><?php echo scrub_out($level->quad->name); ?></td>
</tr>
<tr>
  <th>Level</th><td><?php echo scrub_out($level->catalog_id); ?></td>
  <th><abbr title="Lithostratoigraphic Unit">L. U.</abbr></th><td><?php echo scrub_out($level->lsg_unit->name); ?></td>
</tr>
<tr>
  <th>Northing</th><td><?php echo scrub_out($level->northing); ?></td>
  <th>Easting</th><td><?php echo scrub_out($level->easting); ?></td>
</tr>
<tr>
  <th>Elevation Order</th><td colspan="3"><?php echo $zorder = $level->z_order == 'asc' ? 'Ascending' : 'Descending'; ?></td>
</tr>
</table>
<h5>Elevations</h5>
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
<h5>Excavators</h5>
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
<h5>Notes</h5>
<table class="table table-hover table-bordered table-white">
<tr>  
  <th>Describe: Sediment, Artifacts, Krotovina, Features</th>
</tr>
<tr>
  <td><?php echo scrub_out($level->description); ?></td>
</tr>
<tr>
  <th>Describe the differences and similarities compared to the last level.</th>
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
<tr>
  <th>General Notes</th>
</tr>
<tr>
  <td><?php echo scrub_out($level->other); ?></td>
</tr>
</table>
<ul class="nav nav-tabs" id="media_nav">
  <li class="active"><a href="#picture" data-toggle="tab">Images</a></li>
  <li><a href="#3dmodel" data-toggle="tab">3D Models</a></li>
  <li><a href="#media" data-toggle="tab">Other Media</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="picture">
    <?php require_once \UI\template('/level/images'); ?>
  </div>
  <div class="tab-pane" id="3dmodel">
    <?php require_once \UI\template('/level/3dmodel'); ?>
  </div>
  <div class="tab-pane" id="media">
    <?php require_once \UI\template('/level/media'); ?>
  </div>
</div>
