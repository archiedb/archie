<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<h3>Reports</h3>
<i>Report requests are acted on every five minutes. Once your report is complete you will receive an e-mail notification.</i>
<?php Event::display(); ?>
<fieldset>
<legend>CSV Reports</legend>
</fieldset>
<?php Error::display('general'); ?>
<div class="row">
  <div class="span2"><strong>Report Type</strong></div>
  <div class="span3"><strong>Report Date</strong></div>
</div>
<div class="row">
  <?php $report = new Report('csv','siterecord'); ?>
  <div class="span2">Site Records</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/siterecord/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/siterecord/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','allrecord'); ?>
  <div class="span2">All Site(s) Records</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-small btn-info disabled">Rebuild</a>
      <a class="btn btn-small btn-success disabled">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','sitelevel'); ?>
  <div class="span2">Site Levels</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','alllevel'); ?>
  <div class="span2">All Site(s) Levels</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitelevel/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','sitefeature'); ?>
  <div class="span2">Site Features</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitefeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitefeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>

<div class="row">
  <?php $report = new Report('csv','allfeature'); ?>
  <div class="span2">All Site(s) Features</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/allfeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/allfeature/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>

<div class="row">
  <?php $report = new Report('csv','sitekrotovina'); ?>
  <div class="span2">Site Krotovina</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/sitekrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/sitekrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>

<div class="row">
  <?php $report = new Report('csv','allkrotovina'); ?>
  <div class="span2">All Site(s) Krotovina</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/allkrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Rebuild</a>
      <a class="btn btn-success btn-small disabled" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/allkrotovina/<?php echo scrub_out(\UI\sess::$user->site->uid); ?>">Download</a>
    </p>
  </div>
</div>

<!--
<fieldset>
<legend>Graphs</legend>
</fieldset>
-->
