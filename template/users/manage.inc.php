<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$active_name = $filter . '_active';
${$active_name} = ' class="active"';
?>
<h4>Archie Users</h4>
<ul class="nav nav-tabs">
  <li<?php echo $enabled_active; ?>>
    <a href="<?php echo Config::get('web_path'); ?>/users/manage">Enabled</a>
  </li>
  <li<?php echo $disabled_active; ?>>
    <a href="<?php echo Config::get('web_path'); ?>/users/manage/disabled">Disabled</a>
  </li>
  <li<?php echo $all_active; ?>>
    <a href="<?php echo Config::get('web_path'); ?>/users/manage/all">All</a>
  </li>
</ul>
<table class="table table-bordered table-hover">
<thead>
<tr>
  <th>Name (Username)</th>
  <th>Email</th>
  <th>Access Level</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $user) { require \UI\template('template/users/manage_row.inc.php'); } ?>
</tbody>
</table>
</div><!-- End table container --> 
