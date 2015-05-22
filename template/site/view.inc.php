<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$accession = strlen($site->accession) ? '[ Acc # ' . scrub_out($site->accession) . ' ]' : '';
?>
<?php Event::display('errors'); ?>
<div class="pull-left">
  <h4><?php echo scrub_out($site->name); ?> Site <?php echo $accession; ?></h4>
</div>
<p class="pull-right text-right">
  <a class="btn btn-primary " role="button" data-toggle="modal" href="#set_project">Set Project</a>
  <a class="btn btn-primary " role="button" data-toggle="modal" href="#set_accession">Set Accession</a>
</p>
<table class="table table-hover table-bordered table-white">
<tr>
  <td>Name</td>
  <td>
    <?php echo scrub_out($site->name); ?>
  </td>
  <td>
    Principal Investigator
  </td>
  <td>
      <?php echo scrub_out($site->principal_investigator); ?>
  </td>
</tr>
<tr>
  <td>
   Description
  </td>
  <td>
      <?php echo scrub_out($site->description); ?>
  </td>
  <td>
    Partners
  </td>
  <td>
      <?php echo scrub_out($site->partners); ?>
  </td>
</tr>
<tr>
  <td>
    Elevation
  </td>
  <td>
      <?php echo scrub_out($site->elevation); ?>
  </td>
  <td>
    Northing
  </td>
  <td>
    <?php echo scrub_out($site->northing); ?>
  </td>
</tr>
<tr>
  <td>
    Easting
  </td>
  <td>
    <?php echo scrub_out($site->easting); ?>
  </td>
  <td>
    Project
  </td>
  <td>
    <?php echo scrub_out($site->project); ?>
  </td>
</tr>
</table>
<?php $accessions = $site->get_all_data('accession'); ?>
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
