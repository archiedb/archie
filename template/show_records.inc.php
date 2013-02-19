<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<h3>RECORDS <?php echo scrub_out(intval($_POST['offset'])); ?> - <?php echo scrub_out(intval($_POST['offset']+Config::get('page_limit'))); ?> out of <?php echo scrub_out($GLOBALS['total']); ?></h3>
<?php 
	if ($_POST['offset'] < Config::get('page_limit')) { $low = '0'; } 
	else { $low = intval($_POST['offset'])-intval(Config::get('page_limit')); } 
	if ($GLOBALS['total'] > $_POST['offset'] + intval(Config::get('page_limit'))) { $high = $_POST['offset']+intval(Config::get('page_limit')); } 
	else { $high = intval($_POST['offset']); } 
?>
<ul class="pager"><li>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($low); ?>" />
<input type="submit" value="Prev" />
</form>
</li><li>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($high); ?>" />
<input type="submit" value="Next" />
</form>
</li></ul>
<table class="table table-hover table-bordered table-condensed">
  <thead>
  <tr>
    <th>Catalog</th>
  	<th>R.N.</th>
	  <th>Unit</th>
  	<th>Quad</th>
  	<th>Level</th>
  	<th>Feature</th>
  	<th>L.U.</th>
  	<th>Material</th>
  	<th>Class.</th>
    <th>&nbsp;</th>
	<!--<div class="table-cell">Date</th>
	<div class="table-cell">User</th>--> 
  </tr>
  </thead>
  <tbody>
<?php foreach ($records as $record) { ?>
<?php require 'template/show_record_row.inc.php'; ?>
<?php } ?>
  </tbody>
</table>
<ul class="pager">
<li>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($low); ?>" />
<input type="submit" value="Prev" />
</form>
</li>
<li>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($high); ?>" />
<input type="submit" value="Next" />
</form>
</li>
</ul>
