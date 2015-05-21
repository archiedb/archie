<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php Event::display('errors'); ?>
<div class="pull-left">
  <h4><?php echo scrub_out($site->name); ?> Site</h4>
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
  <td colspan="3">
    <?php echo scrub_out($site->easting); ?>
  </td>
</tr>
</table>
<?php 
  include \UI\template('/site/modal_set_project');
  include \UI\template('/site/modal_set_accession');
?>
<h5>Current Accession: <span class="btn disabled"><?php echo $site->accession; ?></span></h5>
<h5>Current Project: <span class="btn disabled"><?php echo $site->project; ?></span></h5>
