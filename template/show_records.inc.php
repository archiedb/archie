<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
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
<table class="data">
<tr>
	<th class="action">&nbsp;</th>
	<th class="rn" >R.N.</th>
	<th class="item">Catalog #</th>
	<th class="unit">Unit</th>
	<th>Quad</th>
	<th>Level</th>
	<th class="feature">Feature</th>
	<th class="lu">L.U.</th>
	<th>Material</th>
	<th>Class.</th>
	<th>Date</th>
	<th>Notes</th>
	<th>User</th>
	<?php if ($GLOBALS['user']->access >= 100) { ?>
	<th class="action">&nbsp;</th>
	<?php } ?>
</tr>
<?php foreach ($records as $record) { ?>
<?php require 'template/show_record_row.inc.php'; ?>
<?php } ?>
</table> 
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
