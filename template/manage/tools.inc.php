<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
  <h3>Image Maintenance</h3>
  <em>These tasks are scheduled and run every 5 min. Some of these tasks may take a long time to complete. You will be e-mailed when it has finished</em>
</div>
<div class="row">
  <div class="span2"><strong>Task</strong></div>
  <div class="span3"><strong>Status</strong></div>
</div>
<div class="row">
  <?php $cron = new Cron('qrcode'); ?>
  <div class="span2">QRCodes</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/qrcode">Regenerate</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $cron = new Cron('thumb'); ?>
  <div class="span2">Thumbnails</div>
  <div class="span3"><?php echo $cron->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/manage/regenerate/thumbnail">Regenerate</a>
    </p>
  </div>
</div>

