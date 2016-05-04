<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$accession = strlen($site->accession) ? '[ Acc # ' . scrub_out($site->accession) . ' ]' : '';
?>
<?php Event::display('errors'); ?>
<div class="pull-left">
  <h4><?php echo scrub_out($site->name); ?> Site
  <?php echo \UI\boolean_word($site->enabled,'Enabled'); ?></h4>
</div>
<p class="pull-right text-right">
  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add_field">Add Field</button>
  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#set_project_<?php $site->_print('uid'); ?>">Set Project</button>
  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#set_accession_<?php $site->_print('uid'); ?>">Set Accession</button>
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/manage/site/edit/<?php $site->_print('uid'); ?>">Edit</a>
</p>
<div class="clearfix"></div>
<div class="panel panel-default">
  <div class="panel-heading"><strong>Description</strong></div>
  <div class="panel-body"><?php echo scrub_out($site->description); ?></div>
</div>

<table class="table table-hover">
<tr>
  <th>Name</th>
  <td>
    <?php echo scrub_out($site->name); ?>
  </td>
  <th>Principal Investigator</th>
  <td>
      <?php echo scrub_out($site->principal_investigator); ?>
  </td>
</tr><tr>
  <th>Current Project</th>
  <td>
    <?php echo $site->_print('project'); ?>
  </td>
  <th>Current Accession</th>
  <td>
    <?php echo $site->_print('accession'); ?>
  </td>
</tr><tr>
  <th>Partners</th>
  <td>
      <?php echo scrub_out($site->partners); ?>
  </td>
  <th>Easting</th>
  <td>
    <?php echo scrub_out($site->easting); ?>
  </td>
</tr><tr>
  <th>Elevation</th>
  <td>
      <?php echo scrub_out($site->elevation); ?>
  </td>
  <th>Northing</th>
  <td>
    <?php echo scrub_out($site->northing); ?>
  </td>
</tr><tr>
  <th>Excavation Start</th>
  <td><?php echo $site->excavation_start > 0 ? date('d-M-Y',$site->excavation_start) : 'N/A'; ?></td>
  <th>Excavation End</th>
  <td><?php echo $site->excavation_end > 0 ? date('d-M-Y',$site->excavation_end) : 'N/A'; ?></td>
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
<?php 
  // FIXME: Do this a better way?
  $title = $site->get_valid_settings();
foreach ($site->settings as $key=>$value) { 
  if ($key == 'fields') { continue; }
?>
<tr>
  <td><?php echo scrub_out(ucfirst($title[$key])); ?></td>
  <td><?php \UI\print_var($site->get_setting($key)); ?></td>
  <td><button type="button" data-target="#editsetting<?php echo scrub_out($key); ?>" data-toggle="modal" class="btn btn-primary">Edit</button>
    <?php include \UI\template('/site/modal_edit_setting'); ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php $fields = $site->get_setting('fields'); ?>
<?php if (count($fields)) { ?>
<h4>Additional Fields</h4>
<table class="table table-hover table-striped">
<tbody>
<tr>
  <th>Type</th>
  <th>Field Name</th>
  <th>Field Type</th>
  <th>Field Validation</th>
  <th>Enabled</th>
  <th>&nbsp;</th>
</tr>
<?php foreach ($fields as $fielduid=>$field) { ?>
<tr>
  <td>Record</td>
  <td><?php echo ucfirst(str_replace('_',' ',$field['name'])); ?></td>
  <td><?php echo ucfirst($field['type']); ?></td>
  <td><?php echo ucfirst($field['validation']); ?></td>
  <td><?php echo \UI\boolean_word($field['enabled'],'Enabled'); ?></td>
  <td>
  </td>
</tr>
<?php } // end foreach fields ?>
</tbody>
</table>
<?php } // end fields if ?>
<?php $accessions = $site->get_all_data('accession'); ?>
<?php array_shift($accessions); ?>
<?php if (count($accessions)) { ?>
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
<?php } // end foreach accessions ?>
</tbody>
</table>
<?php } // end if count accessions ?>
<?php $projects = $site->get_all_data('project'); ?>
<?php array_shift($projects); ?>
<?php if (count($projects)) { ?>
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
<?php } ?>
<?php 
  include \UI\template('/site/modal_set_project');
  include \UI\template('/site/modal_set_accession');
  include \UI\template('/site/modal_add_field'); 
?>
