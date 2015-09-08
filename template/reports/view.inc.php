<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<h3>Reports for <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
<small>Report requests are acted on every five minutes. Once your report is complete you will receive an e-mail notification.</small>
</div>
<?php Event::display(); ?>
<h4>CSV Reports</h4>
<?php Error::display('general'); ?>
<div class="row">
  <div class="col-md-4"><strong>Report Type</strong></div>
  <div class="col-md-5 text-right"><strong>Report Date</strong></div>
</div>
<div class="row">
  <?php $report = new Report('csv','siterecord'); ?>
  <div class="col-md-4">Site Records</div>
  <div class="col-md-5 text-right"><?php echo $report->state(); ?></div>
  <div class="col-md-3">
    <p class="pull-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/siterecord/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/siterecord/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','sitelevel'); ?>
  <div class="col-md-4">Site Levels</div>
  <div class="col-md-5 text-right"><?php echo $report->state(); ?></div>
  <div class="col-md-3">
    <p class="pull-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>

<div class="row">
  <?php $report = new Report('csv','sitefeature'); ?>
  <div class="col-md-4">Site Features</div>
  <div class="col-md-5 text-right"><?php echo $report->state(); ?></div>
  <div class="col-md-3">
    <p class="pull-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitefeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitefeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','sitekrotovina'); ?>
  <div class="col-md-4">Site Krotovina</div>
  <div class="col-md-5 text-right"><?php echo $report->state(); ?></div>
  <div class="col-md-3">
    <p class="pull-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitekrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitekrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','sitespatialdata'); ?>
  <div class="col-md-4">Site Spatial Data</div>
  <div class="col-md-5 text-right"><?php echo $report->state(); ?></div>
  <div class="col-md-3">
    <p class="pull-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitespatialdata/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitespatialdata/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
