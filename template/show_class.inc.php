<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
?>
<select name="classification" >
	<option value="">&nbsp;</option> 
<?php foreach ($classes as $class) { 
	$is_selected = ''; 
	if ($class->uid == $class_id) { $is_selected = " selected=\"selected\""; } 
?>
	<option value="<?php echo scrub_out($class->uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($class->name); ?></option> 
<?php } ?>
</select>
