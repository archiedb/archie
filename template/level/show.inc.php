<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <small class="pull-right">
  <div class="btn-group">
    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">Sort Levels By <span class="caret"></span></a>
      <ul class="dropdown-menu">
      <?php 
        foreach (View::get_allowed_sorts('level') as $field) { 
      ?>
        <li><a href="<?php echo Config::get('web_path'); ?>/level/sort/<?php echo scrub_out($field); ?>"><?php echo scrub_out(\UI\field_name($field)); ?></a></li>
      <?php } ?>
      </ul>
  </div>
  </small>
  <h3>
    Levels for site <?php echo scrub_out(\UI\sess::$user->site->name); ?>
  </h3>
</div>
<?php require \UI\template('/page_header'); ?>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
	  <th><a href="<?php echo Config::get('web_path'); ?>/level/sort/unit">Unit</a><?php $view->display_sort('unit'); ?></th>
  	<th><a href="<?php echo Config::get('web_path'); ?>/level/sort/quad">Quad</a><?php $view->display_sort('quad'); ?></th>
    <th><a href="<?php echo Config::get('web_path'); ?>/level/sort/catalog_id">Level</a><?php $view->display_sort('catalog_id'); ?></th>
    <th><a href="<?php echo Config::get('web_path'); ?>/level/sort/lsg_unit"><abbr title="Lithostratoigraphic Unit">L. U.</abbr></a><?php $view->display_sort('lsg_unit'); ?></th>
    <th><a href="<?php echo Config::get('web_path'); ?>/level/sort/closed">Closed</a><?php $view->display_sort('closed'); ?></th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php foreach ($levels as $uid) { 
  $level = new Level($uid); 
  require \UI\template('/level/show_row');
}
?>
  </tbody>
</table>
<?php require \UI\template('/page_header'); ?>
