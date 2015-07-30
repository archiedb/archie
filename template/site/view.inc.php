<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$accession = strlen($site->accession) ? '[ Acc # ' . scrub_out($site->accession) . ' ]' : '';
?>
<?php Event::display('errors'); ?>
<div class="pull-left">
  <h4><?php echo scrub_out($site->name); ?> Site</h4>
</div>
<p class="pull-right text-right">
  <a class="btn btn-primary " role="button" data-toggle="modal" href="#set_project_<?php echo $site->uid; ?>">Set Project</a>
  <a class="btn btn-primary " role="button" data-toggle="modal" href="#set_accession_<?php echo $site->uid; ?>">Set Accession</a>
</p>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Name</th>
  <td>
    <?php echo scrub_out($site->name); ?>
  </td>
  <th>Current Project</th>
  <td>
    <?php echo $site->_print('project'); ?>
  </td>
</tr><tr>
  <th>Current Accession</th>
  <td>
    <?php echo $site->_print('accession'); ?>
  </td>
  <th>Description</th>
  <td>
      <?php echo scrub_out($site->description); ?>
  </td>
</tr><tr>
  <th>Principal Investigator</th>
  <td>
      <?php echo scrub_out($site->principal_investigator); ?>
  </td>
  <th>Partners</th>
  <td>
      <?php echo scrub_out($site->partners); ?>
  </td>
</tr>
<tr>
  <th>Elevation</th>
  <td>
      <?php echo scrub_out($site->elevation); ?>
  </td>
  <th>Northing</th>
  <td>
    <?php echo scrub_out($site->northing); ?>
  </td>
</tr>
<tr>
  <th>Easting</th>
  <td>
    <?php echo scrub_out($site->easting); ?>
  </td>
  <th>Enabled</th>
  <td><?php echo \UI\boolean_word($site->enabled); ?>
</tr>
</table>
<h4>Site Settings</h4>
<table class="table table-hover table-striped">
<tbody>
<tr>
  <th>Setting</th>
  <th>Value</th>
  <th>&nbsp;</th>
</tr>
<?php foreach (array('units','quads','ticket') as $key) { ?>
<tr>
  <td><?php echo scrub_out(ucfirst($key)); ?></td>
  <td><?php \UI\print_var($site->$key); ?></td>
  <td><a href="#editsetting<?php echo scrub_out($key); ?>" role="button" data-toggle="modal" class="btn">Edit</a>
    <?php include \UI\template('/site/modal_edit_setting'); ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php $accessions = $site->get_all_data('accession'); ?>
<?php array_shift($accessions); ?>
<h4>Accession #'s</h4>
<table class="table table-hover table-striped">
<tbody>
<tr>
  <th>&nbsp;</th>
  <th>Added</th>
  <th>Closed</th>
</tr>
<?php foreach ($accessions as $row) { ?>
<tr>
  <td><strong><?php echo scrub_out($row['accession']); ?></strong></td>
  <td><?php echo date('m-d-Y h:i',$row['created']); ?></td>
  <td><?php echo ($row['closed'] > 0) ? date('m-d-Y',$row['closed']) : 'ACTIVE'; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<?php $projects = $site->get_all_data('project'); ?>
<?php array_shift($projects); ?>
<h4>Projects</h4>
<table class="table table-hover table-striped">
<tbody>
<tr>
  <th>&nbsp;</th>
  <th>Added</th>
  <th>Closed</th>
</tr>
<?php foreach ($projects as $row) { ?>
<tr>
  <td><strong><?php echo scrub_out($row['project']); ?></strong></td>
  <td><?php echo date('m-d-Y h:i',$row['created']); ?></td>
  <td><?php echo ($row['closed'] > 0) ? date('m-d-Y',$row['closed']) : 'ACTIVE'; ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<?php 
  include \UI\template('/site/modal_set_project');
  include \UI\template('/site/modal_set_accession');
?>
