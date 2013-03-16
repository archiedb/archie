<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<fieldset>
<legend>Archie Config Information</legend>
<table class="table table-bordered table-hover">
<thead>
  <th>Setting</th>
  <th>Value</th>
</thead>
<tbody>
<tr>
  <td>Archie Version</td>
  <td><?php echo \UI\boolean_word(\update\Code::check(),'Build:' . \update\Code::version()); ?></td>
</tr>
<tr>
  <td>Archie DB Version</td>
  <td><?php echo \UI\boolean_word(\update\Database::check(),'Current Version:' . \update\Database::version()); ?></td>
</tr>
<tr>
  <td>Log Path:</td>
  <td><?php echo \UI\boolean_word(is_writeable(Config::get('log_path')),Config::get('log_path')); ?></td>
</tr>
<tr>
  <td>Data Path:</td>
  <td><?php echo \UI\boolean_word(is_writeable(Config::get('data_root')),Config::get('data_root')); ?></td>
</tr>
<tr>
  <td>Memory Cache</td>
  <td><?php echo \UI\boolean_word(Config::get('memory_cache')); ?></td>
</tr>
</tbody>
</table>
</fieldset>
<fieldset>
<legend>System Information</legend>
<table class="table table-bordered table-hover">
<thead>
  <th>Setting</th>
  <th>Value</th>
</thead>
<tbody>
<tr>
  <td>PHP-GD Support</td>
  <td><?php echo \UI\boolean_word(\debug\check_gd_support()); ?></td>
</tr>
<tr>
  <td>Override Execution Time</td>
  <td><?php echo \UI\boolean_word(\debug\check_time_limit()); ?></td>
</tr>
<tr>
  <td>QRCode Cache Directory Writeable</td>
  <td><?php echo \UI\boolean_word(\debug\check_qrcode_cache_writeable()); ?></td>
</tr>
</tbody>
</table>
</fieldset>
