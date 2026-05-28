<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once \UI\template('/menu'); ?>
<div class="page-header">
  <h3>
    Curation Institutions
  </h3>
</div>
<?php require \UI\template('/page_header'); ?>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
  	<th>Institution</th>
    <th>Contact</th>
    <th>Status</th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
  <?php 
  foreach ($institutions as $uid) {
    $institution = new Institution($uid);
    require \UI\template('/curation/show_institution_row');
  }
  ?>
  </tbody>
</table>
<?php require \UI\template('/page_header'); ?>
