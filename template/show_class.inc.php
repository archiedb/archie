<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
?>
<?php foreach ($classes as $class) { 
	$is_selected = ''; 
	if ($class->uid == $_POST['classification']) { $is_selected = ' selected="selected"'; } 
?><option value="<?php echo scrub_out($class->uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($class->name); ?></option><?php } ?>
