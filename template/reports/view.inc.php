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
  <?php $report = new Report('csv','site'); ?>
  <div class="span2">Full Site</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-info btn-small" href="<?php echo Config::get('web_path'); ?>/reports/request/csv/site/<?php echo scrub_out(Config::get('site')); ?>">Rebuild</a>
      <a class="btn btn-primary btn-small" href="<?php echo Config::get('web_path'); ?>/reports/download/csv/site/<?php echo scrub_out(Config::get('site')); ?>">Download</a>
    </p>
  </div>
</div>
<div class="row">
  <?php $report = new Report('csv','all'); ?>
  <div class="span2">All Site(s)</div>
  <div class="span3"><?php echo $report->state(); ?></div>
  <div class="span4 offset3">
    <p class="text-right">
      <a class="btn btn-small btn-info disabled">Rebuild</a>
      <a class="btn btn-small btn-primary disabled">Download</a>
    </p>
  </div>
</div>
<fieldset>
<legend>Graphs</legend>
</fieldset>
