<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <h3>Archie Config Information</h3>
</div>
<table class="table table-hover">
<thead>
  <th>Setting</th>
  <th class="text-right">Value</th>
</thead>
<tbody>
<tr>
  <td>Archie Version</td>
  <td class="text-right"><?php echo \UI\boolean_word(\update\Code::check(),'Build:' . \update\Code::version()); ?></td>
</tr>
<tr>
  <td>Archie DB Version</td>
  <td class="text-right"><?php echo \UI\boolean_word(\update\Database::check(),'Current Version:' . \update\Database::version()); ?></td>
</tr>
<tr>
  <td>Archie Config Version</td>
  <td class="text-right"><?php echo \UI\boolean_word(\update\Code::config_check(),'Current Version:' . \update\Code::config_version()); ?></td>
</tr>
<tr>
  <td>Log Path:</td>
  <td class="text-right"><?php echo \UI\boolean_word(is_writeable(Config::get('log_path')),Config::get('log_path')); ?></td>
</tr>
<tr>
  <td>Data Path:</td>
  <td class="text-right"><?php echo \UI\boolean_word(is_writeable(Config::get('data_root')),Config::get('data_root')); ?></td>
</tr>
<tr>
  <td>Memory Cache</td>
  <td class="text-right"><?php echo \UI\boolean_word(Config::get('memory_cache')); ?></td>
</tr>
<tr>
  <td>3D Model to PNG</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\model_to_png(Config::get('stl2pov_cmd'),Config::get('megapov_cmd'))); ?></td>
</tr>
</tbody>
</table>
<div class="page-header">
  <h3>System Information</h3>
</div>
<table class="table table-hover">
<thead>
  <th>Setting</th>
  <th class="text-right">Value</th>
</thead>
<tbody>
<tr>
  <td>PHP-GD Support</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_gd_support()); ?></td>
</tr>
<tr>
  <td>Override Execution Time</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_time_limit()); ?></td>
</tr>
<tr>
  <td>PHP Uploads Enabled</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_uploads()); ?></td>
</tr>
<tr>
  <td>PHP Uploads at least 20MB</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_upload_size(),\debug\return_upload_size()); ?></td>
</tr>
<tr>
  <td>Cache Directory Writeable</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_cache_writeable(),Config::get('prefix') . '/lib/cache'); ?></td>
</tr>
<tr>
  <td>ImageMagick Installed</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_imagemagick(),'/usr/bin/convert'); ?></td>
</tr>
<tr>
  <td>Python Modules for Scatterplots</td>
  <td class="text-right"><?php echo \UI\boolean_word(\debug\check_python_scatterplots()); ?></td>
</tr>
</tbody>
</table>
</fieldset>
