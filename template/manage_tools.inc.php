<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="text-right">
  <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/qrcode">Regenerate QRCodes</a>
  <a class="btn" href="<?php echo Config::get('web_path'); ?>/magage/database/optimize">Optimize Database</a>
</p>
<div class="content-block">
<fieldset>
<legend>System Information</legend>
<table class="table table-bordered table-white">
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
</div><!-- End content block -->
