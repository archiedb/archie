<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once \UI\template('/menu'); ?>
<div class="page-header">
  <small class="pull-right">
  <div class="btn-group">
    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">Sort Records By <span class="caret"></span></a>
      <ul class="dropdown-menu">
      <?php 
        foreach (View::get_allowed_sorts('curation') as $field) { 
      ?>
        <li><a href="<?php echo Config::get('web_path'); ?>/curation/sort/<?php echo scrub_out($field); ?>"><?php echo scrub_out(\UI\field_name($field)); ?></a></li>
      <?php } ?>
      </ul>
  </div>
  </small>
  <h3>
    Curation Records
  </h3>
</div>
<?php require \UI\template('/page_header'); ?>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
    <th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/uid">Curation #</a><?php $view->display_sort('uid'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/institution">Institution</a><?php $view->display_sort('institution'); ?></th>
	  <th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/unit">Building</a><?php $view->display_sort('building'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/room">Room</a><?php $view->display_sort('room'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/cabinet">Cabinet</a><?php $view->display_sort('cabinet'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/drawer">Drawer</a><?php $view->display_sort('drawer'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/curation/sort/status">Status</a><?php $view->display_sort('status'); ?></th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
  <?php 
  foreach ($curations as $uid) {
    $curation = new Curation($uid);
    require \UI\template('/curation/show_row');
  }
  ?>
  </tbody>
</table>
<?php require \UI\template('/page_header'); ?>
