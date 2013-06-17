<?php
define('CLI',true); 
require_once '../class/init.php';

$handle = fopen('10IH73-14411.csv','r');
$total = 1; 
$bad = 0; 
$good = 0; 
$skip = 0; 

Config::set('site','10IH73',1); 

while (($raw = fgetcsv($handle)) !== FALSE) { 
//foreach ($lines as $row ) { 

	$total++; 

	$input = array(); 

	// Skip if something in this field
	if (strlen(trim($raw['7']))) { $skip++; continue; } 

	// Pre-formating
	if (strlen(trim($raw['5']))) { 
		$classification_id = Classification::name_to_id(trim($raw['8'])); 
	} 
	// Can I default to other? 
	else { 
		$classification_id = null;
	} 	

	$input['site'] 		= '10IH73'; 
	$input['catalog_id']	= intval(trim($raw['1'])) ? intval(trim($raw['1'])) : null;
	$input['unit'] 		= trim($raw['2']); 
	$input['level'] 	= is_numeric($raw['4']) ? $raw['4'] : null; 
	$input['lsg_unit'] 	= lsgunit::name_to_id($raw['20']); 
	$input['station_index'] = preg_replace('/[^0-9]/','',$raw['0']); // * ?'s on this value, removing them
	$input['classification'] = $classification_id; 
	$input['xrf_matrix_index'] = null; 


	$input['height'] 	= null;
	$input['width'] 	= $raw['13']; 
	$input['thickness'] 	= $raw['14']; 
	$input['quanity'] 	= preg_replace('/[^0-9]/','',trim($raw['9'])); // * and ?'s on this value, removing them 
	$input['xrf_artifact_index'] = null; 
	$input['material'] 	= material::name_to_id($raw['6']); 
	$input['quad'] 		= quad::name_to_id(strtoupper($raw['3'])); 
	$input['notes'] 	= $raw['15'] . ' ' . $raw['16'];  
	$input['feature'] 	= null; 
	$input['user']		= '-1'; 

	if (Record::validate($input)) { 
		// Do Stuff
		if (Record::create($input)) { 
			$good++; 
		} 
		else { 
			$bad++; 
			print "LINE:$total\n"; 
			print Error::dump(); 
			print_r($input); 
			print "------------------\n"; 
		} 
	} 
	else { 
		$bad++; 
		print "LINE:$total\n";
		print Error::dump(); 
		print_r($input); 
		print "------------------\n"; 
	} 


} 

// Hows it looking? 
echo "Good: $good / Bad: $bad / Skip: $skip\n"; 

?>
