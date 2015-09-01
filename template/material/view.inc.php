<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
  <p class="pull-right text-right">
    <a class="btn btn-success" href="<?php echo Config::get('web_path'); ?>/manage/material/add">Add Material</a>
  </p>
  <h4>Materials</h4>
</div>
<table class="table table-hover">
<thead>
<tr>
  <th>Name</th>
  <th>Records</th>
  <th>Enabled</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach ($materials as $material) { require \UI\template('/material/view_row'); } ?>
</tbody>
</table>
