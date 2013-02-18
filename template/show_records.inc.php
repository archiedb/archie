<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="content-block">
<h3>RECORDS <?php echo scrub_out(intval($_POST['offset'])); ?> - <?php echo scrub_out(intval($_POST['offset']+Config::get('page_limit'))); ?> out of <?php echo scrub_out($GLOBALS['total']); ?></h3>
<?php 
	if ($_POST['offset'] < Config::get('page_limit')) { $low = '0'; } 
	else { $low = intval($_POST['offset'])-intval(Config::get('page_limit')); } 
	if ($GLOBALS['total'] > $_POST['offset'] + intval(Config::get('page_limit'))) { $high = $_POST['offset']+intval(Config::get('page_limit')); } 
	else { $high = intval($_POST['offset']); } 
?>
<div id="view">
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($low); ?>" />
<input type="submit" value="Prev" />
</form>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($high); ?>" />
<input type="submit" value="Next" />
</form>
<div class="table-container">
<div class="table-header">
	<div class="table-cell">Catalog</div>
	<div class="table-cell">R.N.</div>
	<div class="table-cell">Unit</div>
	<div class="table-cell">Quad</div>
	<div class="table-cell">Level</div>
	<div class="table-cell">Feature</div>
	<div class="table-cell">L.U.</div>
	<div class="table-cell">Material</div>
	<div class="table-cell">Class.</div>
	<div class="table-cell">Date</div>
	<div class="table-cell">User</div>
</div><!-- END table-row -->
<?php foreach ($records as $record) { ?>
<?php require 'template/show_record_row.inc.php'; ?>
<?php } ?>
</div> <!-- end of table-container -->
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($low); ?>" />
<input type="submit" value="Prev" />
</form>
<form style="display:inline;" method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<input type="hidden" name="field" value="<?php echo scrub_out($_POST['field']); ?>" />
<input type="hidden" name="value" value="<?php echo scrub_out($_POST['value']); ?>" />
<input type="hidden" name="offset" value="<?php echo scrub_out($high); ?>" />
<input type="submit" value="Next" />
</form>
</div> <!-- End View --> 
</div> <!-- End of content block --> 
