<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php Event::display('errors'); ?>
<div class="pull-left">
  <h4><?php echo scrub_out($site->name); ?> Site</h4>
</div>
<p class="pull-right text-right">
  <a class="btn btn-primary disabled" href="#addproject">Set Project</a>
  <a class="btn btn-primary disabled" href="#addaccession">Set Accession</a>
</p>
<table class="table table-hover table-bordered table-white">
<tr>
  <td>Name</td>
  <td>
    <?php echo scrub_out($site->name); ?>
  </td>
</tr>
<tr>
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
</tr>
<tr>
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
</tr>
<tr>
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
</tr>
</table>
