<?php 
require_once '../class/init.php';

$contents = file_get_contents('list.csv'); 
$lines = explode("\n",$contents); 

foreach ($lines as $row) { 

	list($material,$classification) = explode(",",$row); 
		
	if (!strlen($material) OR !strlen($classification)) { continue; } 
	
	$material = Dba::escape($material); 

	// Try to find the material
	$sql = "SELECT * FROM `material` WHERE `name`='$material'"; 
	$db_results = Dba::read($sql); 
	$row = Dba::fetch_assoc($db_results); 
	
	if (!$row['uid']) { 
		$sql = "INSERT INTO `material` SET `name`='$material'"; 
		$db_results = Dba::write($sql); 
		$material_id = Dba::insert_id(); 
	} 
	else { 
		$material_id = $row['uid']; 
	}  

	$classification = Dba::escape($classification); 


	//Try to find the classification
	$sql = "SELECT * FROM `classification` WHERE `name`='$classification'"; 
	$db_results = Dba::read($sql); 
	$row = Dba::fetch_assoc($db_results); 

	if (!$row['uid']) { 
		$sql = "INSERT INTO `classification` SET `name`='$classification'"; 
		$db_results = Dba::write($sql); 
		$class_id = Dba::insert_id();  
	} 
	else { 
		$class_id = $row['uid']; 
	} 

	$sql = "INSERT INTO `material_classification` (`material`,`classification`) VALUES ('$material_id','$class_id')"; 
	$db_results = Dba::write($sql); 

} 
?>
