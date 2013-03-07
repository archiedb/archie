<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<h3>Records for site <?php echo Config::get('site'); ?></h3>
<?php require \UI\template('/page_header'); ?>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
    <th><a href="<?php echo Config::get('web_path'); ?>/records/sort/catalog_id">Catalog</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/station_index">R.N.</a></th>
	  <th><a href="<?php echo Config::get('web_path'); ?>/records/sort/unit">Unit</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/quad">Quad</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/level">Level</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/feature">Feature</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/lsg_unit">L.U.</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/material">Material</a></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/records/sort/classification">Class.</a></th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php foreach ($records as $uid) { 
  $record = new Record($uid); 
?>
<?php require 'template/show_record_row.inc.php'; ?>
<?php } ?>
  </tbody>
</table>
<?php require \UI\template('/page_header'); ?>
