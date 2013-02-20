<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
$class_id = $class_id ? $class_id : $_POST['classification']; 
?>
<?php foreach ($classes as $class) { 
	$is_selected = ''; 
	if ($class->uid == $class_id) { $is_selected = ' selected="selected"'; } 
?><option value="<?php echo scrub_out($class->uid); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($class->name); ?></option><?php } ?>
