<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <small class="pull-right">
  <div class="btn-group">
    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">Sort Krotovina By <span class="caret"></span></a>
      <ul class="dropdown-menu">
      <?php 
        foreach (View::get_allowed_sorts('krotovina') as $field) { 
      ?>
        <li><a href="<?php echo Config::get('web_path'); ?>/krotovina/sort/<?php echo scrub_out($field); ?>"><?php echo scrub_out(\UI\field_name($field)); ?></a></li>
      <?php } ?>
      </ul>
  </div>
  </small>
  <h3>
    Krotovinas for site <?php echo scrub_out(\UI\sess::$user->site->name); ?>
  </h3>
</div>
<?php require \UI\template('/page_header'); ?>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
    <th><a href="<?php echo Config::get('web_path'); ?>/krotovina/sort/catalog_id">Catalog #</a> <?php $view->display_sort('catalog_id'); ?></th>
  	<th>Keywords</th>
    <th>Description</th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php foreach ($krotovinas as $uid) { 
  $krotovina = new Krotovina($uid); 
  require \UI\template('/krotovina/show_row');
}
?>
  </tbody>
</table>
<?php require \UI\template('/page_header'); ?>
