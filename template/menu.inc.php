<div id="menu">
<div class="row">
<h1>Archie - <?php echo scrub_out(Config::get('site')); ?></h1> 
<form method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search">
<select name="field">
	<option value="item">Item</option> 
	<option value="station_index">RN</option>
	<option value="notes">Notes</option> 
	<option value="feature">Feature</option> 
	<option value="unit">Unit</option>
	<option value="weight">Weight</option>
	<option value="height">Height</option>
	<option value="width">Width</option>
	<option value="thickness">Thickness</option>
	<option value="quanity">Quanity</option>
</select>
<input type="textbox" name="value" value="<?php echo scrub_out($_POST['value']); ?>"/>
<input type="submit" value="Search" />
</form> 

</div>
<div class="row">
<span class="button" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/new.php';"> New </span> 
<span class="button" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/view.php';"> View </span>
<?php if ($GLOBALS['user']->access == '100') { ?>
<span class="button" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/admin.php?action=export&type=csv';"> CSV </span>
<span class="button" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/admin.php?action=manage';"> Manage </span>
<?php } ?>
</div>
</div>
<div id="content"> 
