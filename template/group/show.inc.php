<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="pull-left">
<h4>User Groups</h4>
</div>
<p class="pull-right text-right">
  <a class="btn btn-success" href="<?php echo Config::get('web_path'); ?>/manage/group/showcreate">Create New Group</a>
</p>
<table class="table table-bordered table-hover">
<thead>
<tr>
  <th>Name</th>
  <th>Description</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach ($groups as $group) { require \UI\template('/group/show_row'); } ?>
</tbody>
</table>
</div><!-- End table container --> 
