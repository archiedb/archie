<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <h3>Image Maintenance</h3>
  <em>These tasks are scheduled and run every 5 min. Some of these tasks may take a long time to complete. You will be e-mailed when it has finished</em>
</div>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<?php Event::display('warnings'); ?>
<div class="row">
  <div class="col-md-2"><strong>Task</strong></div>
  <div class="col-md-3"><strong>Status</strong></div>
</div>
<div class="row">
  <?php $cron = new Cron('qrcode'); ?>
  <div class="col-md-2">QRCodes</div>
  <div class="col-md-3"><?php echo $cron->state(); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/qrcode">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('ticket'); ?>
  <div class="col-md-2">Tickets</div>
  <div class="col-md-3"><?php echo $cron->state(); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/ticket">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('thumb'); ?>
  <div class="col-md-2">Image Thumbs</div>
  <div class="col-md-3"><?php echo $cron->state(); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/thumbnail">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('3dmodel_thumb'); ?>
  <div class="col-md-2">3D Model Thumbs</div>
  <div class="col-md-3"><?php echo $cron->state(); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/3dmodel_thumb">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('scatterplots'); ?>
  <div class="col-md-2">Level Report Plots</div>
  <div class="col-md-3"><?php echo $cron->state(); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/scatterplots">Regenerate</a>
    </p>
  </div>
</div>

<div class="page-header">
  <h3>System Maintenance</h3>
</div>
<div class="row">
  <div class="col-md-2">Rebuild Config File</div>
  <div class="col-md-3"><?php echo \UI\boolean_word(\update\Code::config_check(),'Current Version:' . \update\Code::config_version()); ?></div>
  <div class="col-md-4">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/rebuildconfig">Rebuild Config</a>
    </p>
  </div>
</div>
<div class="row">
  <?php 
    $codeCheck = \update\Code::check();
    $gitVersion = \update\Code::git_version();
  ?>
  <div class="col-md-2">Update Archie</div>
  <div class="col-md-3"><?php echo \UI\boolean_word($codeCheck,'Latest Version:' . $gitVersion); ?></div>
  <div class="col-md-4">
    <p class="text-right">
<?php 
  if (!$codeCheck) { $btn_class = ' btn-danger'; $update_needed = 'Update Needed'; } 
  else { $btn_class = ' disabled btn-success'; $update_needed = 'Up to Date'; }
?>
      <a class="btn<?php echo $btn_class; ?>" href="<?php echo Config::get('web_path'); ?>/manage/updatearchie"><?php echo $update_needed; ?></a>
    </p>
  </div>
</div>
</div>
