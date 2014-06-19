<?php
define('CLI',true); 
require_once '../class/init.php';

Config::set('site','10IH73',1); 

/* Plan:
 * - Select all records
 * - Check for existance of a level record
 * -- If found then 
 * --- if record check excavators, if record.creator != excavator add it
 * -- if no level then create level
 * --- set excavator #1 to record creator
 **/ 

$retval = true; 

// Remove junk from record.feature
$sql = "UPDATE `record` SET `feature`='' WHERE `feature`='s' OR `feature`='CENTER SED SAMPLE' OR `feature`='SOUTH SED SAMPLE' OR `feature`='EAST SED SAMPLE'";
$db_results = Dba::write($sql); 

// Make K733 Pt. 24 just K733
$sql = "UPDATE `record` SET `feature`='K733' WHERE `feature`='K733 Pt. 24'";
$db_results = Dba::write($sql);

// Make sure that all records have a site created for them, if
// a record doesn't have it print it out and exit

$sql = "SELECT `record`.`catalog_id`,`record.`site`,`site`.`name` FROM `record` INNER JOIN `site` ON `site`.`name`=`record`.`site` WHERE `site`.`name` IS NULL";
$db_results = Dba::read($sql); 

$unknown_sites = array();
$message = '';

while ($row = Dba::fetch_assoc($db_results)) { 
	$unknown_sites[] = $row['site'];
} 

foreach ($unknown_sites as $site) { 
	$sql = "SELECT * FROM `site` WHERE `name`='" . $site['site'] . "'";
	$db_results = Dba::read($sql); 
	$row = Dba::fetch_assoc($db_results); 
	if ($row['uid']) { 
		$update_sql = "UPDATE `record` SET `site`='" . $row['uid'] . "' WHERE `site`='" . $row['name'] . "'";
		$db_results = Dba::write($sql); 
		echo "Updated Record Site value, set " . $row['name']  . " to UID of site [ " . $row['uid'] . " ]\n";
	}
	else { 
		$retval = false; 
		$message .= "Site:" . $site['site'] . " Not FOUND\n";
	}
} // end foreach

if (!$retval) { 
	echo "Unknown Site(s) Found\n"; 
	echo $message;
	exit;
}

$sql = "SELECT * FROM `record`";
$db_results = Dba::read($sql); 

$records = array();

while ($row = Dba::fetch_assoc($db_results)) { 
	$records[] = $row;
}

$sql = "SELECT * FROM `level`";
$db_results = Dba::read($sql); 

$levels = array();

while ($row = Dba::fetch_assoc($db_results)) { 
	$levels[$row['catalog_id']] = $row;
}

foreach ($records as $record) { 
	echo "Starting work on #" . $record['catalog_id'] . " UID [ " . $record['uid'] . " ]\n";

	// If no level defined, skip
	if (!$record['level']) { 
		echo "\tRecord: #" . $record['catalog_id'] . " [ " . $record['uid'] . " ] has no level skipping \n";
		continue;
	}

	// See if there's a level of record['level'] with the same quad/unit
	if (isset($levels[$record['level']])) { 
		// Now make sure it's really really the same thing
		if ($record['quad'] == $levels[$record['level']]['quad'] AND $record['unit'] == $levels[$record['level']]['unit']) {
			// Ok ok it is the same, move on
			echo "\tRecord: #" . $record['catalog_id'] . " with level L-" . $levels[$record['level']]['catalog_id'] . " found checking excavators\n";
			$excavator = excavator_fix($record['user'],$levels[$record['level']]);
			if (!$excavator) { 
				record_level_fix($record['uid'],$levels[$record['level']]['uid']);
				continue; 
			}
			$levels[$record['level']][$excavator] = $record['user'];
			echo "\tUser: " . $record['user'] . " Added as $excavator ON level L-" . $levels[$record['level']]['catalog_id'] . "\n";
			// Update the record to point at the right (new level) 
			record_level_fix($record['uid'],$levels[$record['level']]['uid']);
		}
		else { 
			echo "\tRecord: #" . $record['catalog_id'] . " doesn't match level L-" . $levels[$record['level']]['catalog_id'] . " searching...\n";
			$sql = "SELECT * FROM `level` WHERE `quad`='" . $record['quad'] . "' AND `unit`='" . $record['unit'] . "' AND `catalog_id`='" . $record['level'] . "'";
			$db_results = Dba::read($sql); 
			$row = Dba::fetch_assoc($db_results);
			// If it doesn't exists we need to create it
			if (!$row['uid']) { 
				$level_uid = migration_create_level($record['user'],$record['level'],$record['unit'],$record['quad'],$record['site']);
				echo "\tLevel Created: [ $level_uid ] - L-" . $record['level'] . "\n";
				$level = array('catalog_id'=>$record['level'],'quad'=>$record['quad'],'unit'=>$record['unit'],'uid'=>$level_uid,'excavator_one'=>$record['user']);
				$levels[$record['level']] = $level;
				record_level_fix($record['uid'],$levels[$record['level']]['uid']);
			}
			// Add to excavators if new
			else { 
				$excavator = excavator_fix($record['user'],$levels[$record['level']]);
				if (!$excavator) { 
					record_level_fix($record['uid'],$row['uid']);
					continue; 
				}
				$levels[$record['level']][$excavator] = $record['user'];
				echo "\tUser: " . $record['user'] . " Added as $excavator ON level L-" . $levels[$record['level']]['catalog_id'] . "\n";
				record_level_fix($record['uid'],$row['uid']);
			}
		}
	} // if level is set
	// No level found
	else { 
		echo "\tRecord: #"  . $record['catalog_id'] . " doesn't match any levels, creating a new one\n";
		$level_uid = migration_create_level($record['user'],$record['level'],$record['unit'],$record['quad']);
		echo "\tLevel Created: [ $level_uid ] - L-" . $record['level'] . "\n";
		$level = array('catalog_id'=>$record['level'],'quad'=>$record['quad'],'unit'=>$record['unit'],'uid'=>$level_uid,'excavator_one'=>$record['user']);
		$levels[$record['level']] = $level;
		record_level_fix($record['uid'],$levels[$record['level']]['uid']);
	}

} // foreach records

unset($levels);
unset($records);

echo "**************************\nSTARTING WORK ON FEATURES\n***************************\n";

// Now load the records and features
$sql = "SELECT * FROM `record`";
$db_results = Dba::read($sql); 

$records = array();
$features = array();
$auto_gen_feature = array();
$bad=0;

// Mapping for crazy ass features
$newfeature = array('PF1'=>50,'PF 1'=>50,'PF1 outside'=>50,'13-1'=>51,'13-2'=>52,'13-3'=>53,'13-4'=>54,'13-6'=>54,'13-7'=>55,'13-8'=>56,
	'E-2'=>57,'e1-1'=>58,'f13-1'=>51,'F13-1'=>51,'O1'=>59,'P1'=>60,'P2'=>61,'P3'=>62,'Sand Mound'=>63,'E1-1'=>58,'F13-3'=>53);
$newkeyfeatures = array_keys($newfeature);

while ($row = Dba::fetch_assoc($db_results)) { 
	$records[] = $row;
}

foreach ($records as $record) { 

	// Cut out any whitespace they added
	$record['feature'] = trim($record['feature']);

	echo "Starting work on Record #" . $record['catalog_id'] . "\n";

	if (!$record['feature']) { 
		echo "\tNo Feature Found skipping...\n";
		continue;
	}


	if (preg_match("/[Kk](\d+)$/",$record['feature'],$matches)) { 
		// This is actually a krotovina
		record_feature_fix($record['uid'],'');
		echo "\tKrotovina found in Feature field, switching " . $record['feature'] . " to Krotovina [ " . $matches['1'] . " ] \n";
		record_krotovina_fix($record['uid'],$matches['1']);
		continue;
	}

	if (preg_match("/Krotovina-\s?(\d+)/",$record['feature'],$matches)) { 
		// This is actually a krotovina
		record_feature_fix($record['uid'],'');
		echo "\tKrotovina found in Feature field, switching " . $record['feature'] . " to Krotovina [ " . $matches['1'] . " ] \n";
		record_krotovina_fix($record['uid'],$matches['1']);
		continue;
	}

	// Add a note then adjust the value of feature
	if (in_array($record['feature'],$newkeyfeatures)) {
		// Update the notes to show we've tweaked the feature
		$record['feature'] = trim($record['feature']);
		echo "\tWARN: Adding " . $record['feature'] . " to notes for [ " . $record['uid'] . " ]\n";
		record_notes_fix($record['uid'],$record['notes'],$record['feature']);
		echo "\tWARN: New Feature Value [ " . $newfeature[$record['feature']] . " ] mapped from [ " . $record['feature'] . " ]\n";
		$record['feature'] = $newfeature[$record['feature']];
	}

	if (!is_numeric($record['feature'])) { 
		echo "\tERROR: Feature not numeric on UID [" . $record['uid'] . "] Feature [" . $record['feature'] . "] If Intval'd [" . intval($record['feature']) . "]\n";
		$bad++; 
		continue;
	}

	// If we've created this feature already
	if (isset($features[$record['feature']])) {
		record_feature_fix($record['uid'],$features[$record['feature']]['uid']);
	}
	else { 
		// Create the krotovina right here and now
		$feature_uid = migration_create_feature($record['feature'],$record['site'],$record['user']);
		echo "\tAdded new Feature Record #" . $record['feature'] . " [ $feature_uid ] for Record [ " . $record['uid'] . " ]\n";
		// Add it to the list of features available
		$features[$record['feature']] = array('uid'=>$feature_uid);
		record_feature_fix($record['uid'],$feature_uid);
	}



} // record


echo "Found $bad bad features\n";

unset($records);
unset($features);

echo "**************************\nSTARTING WORK ON KROTOVINA\n***************************\n";

// Now load the records and krotovinas
$sql = "SELECT * FROM `record`";
$db_results = Dba::read($sql); 

$records = array();
$krotovinas = array();

while ($row = Dba::fetch_assoc($db_results)) { 
	$records[] = $row;
}


foreach ($records as $record) { 

	echo "Starting work on Record #" . $record['catalog_id'] . "\n";

	if (!$record['krotovina']) { 
		echo "\tNo Krotovina Found skipping...\n";
		continue;
	}
	// If we've created this krotovina already
	if (isset($krotovinas[$record['krotovina']])) {
		record_krotovina_fix($record['uid'],$krotovinas[$record['krotovina']]['uid']);
	}
	else { 
		// Create the krotovina right here and now
		$krotovina_uid = migration_create_krotovina($record['krotovina'],$record['site'],$record['user']);
		echo "\tAdded new Krotovina Record [ $krotovina_uid ] for Record [ " . $record['uid'] . " ]\n";
		// Add it to the list of krotovinas available
		$krotovinas[$record['krotovina']] = array('uid'=>$krotovina_uid);
		record_krotovina_fix($record['uid'],$krotovina_uid);
	}


} // end foreach records for krotovinas


/**
 * FUNCTIONS
 */

// hack'n'slash a feature in, does not follow normal rules we have to add it though
function migration_create_feature($catalog_id,$site,$user) { 

	$catalog_id = Dba::escape($catalog_id);
	$site = Dba::escape($site);
	$user = Dba::escape($user);
	$now = time();

	$sql = "INSERT INTO `feature` (`site`,`catalog_id`,`user`,`created`) VALUES ('$site','$catalog_id','$user','$now')";
	$db_results = Dba::write($sql); 

	$insert_id = Dba::insert_id();

	return $insert_id;

} // migration_create_feature

// hack'n'slash a krotovina in, does not follow normal rules we have to add it
function migration_create_krotovina($catalog_id,$site,$user) { 

	$user = Dba::escape($user);
	$catalog_id = Dba::escape($catalog_id);
	$site = Dba::escape($site);
	$now = time();
	$sql = "INSERT INTO `krotovina` (`site`,`catalog_id`,`user`,`created`) VALUES ('$site','$catalog_id','$user','$now')";
	$db_results = Dba::write($sql); 

	$insert_id = Dba::insert_id(); 

	return $insert_id;

} // migration_create_krotovina

// Hack'n'slash doesn't follow normal rules because this is a "shit its too late garbage in guess garbage is coming out" type deal
function migration_create_level($user,$catalog_id,$unit,$quad,$site) {

	$user = Dba::escape($user);
	$catalog_id = Dba::escape($catalog_id);
	$unit = Dba::escape($unit);
	$quad = Dba::escape($quad);
	$site = Dba::escape($site);
	$now = time();
	$sql = "INSERT INTO `level` (`user`,`site`,`catalog_id`,`unit`,`quad`,`created`,`excavator_one`) VALUES ('$user','$site','$catalog_id','$unit','$quad','$now','$user')";
	$db_results = Dba::write($sql);

	$insert_id = Dba::insert_id();

	return $insert_id;

} // migration_create_level

// Make sure this user is listed on the excavators list, if it's not already full
function excavator_fix($user,$level) {

	$uid = Dba::escape($level['uid']);
	$clean_user = Dba::escape($user);

	if (!$user) { 
		echo "\tExcavator is blank, skipping\n";
		return false;
	}

	// See if this user is listed already
	if ($level['excavator_one'] == $user OR $level['excavator_two'] == $user OR $level['excavator_three'] == $user OR $level['excavator_four'] == $user) {
		// return false nothing done/fixed
		echo "\tExcavator [ $user ] found on L-" . $level['catalog_id'] . " skipping\n";
		return false; 
	}
	// Crap not listed, ok any free spots, if so JAM IT IN THERE
	if (!$level['excavator_one']) {
		$field = 'excavator_one';
	}
	elseif (!$level['excavator_two']) { 
		$field = 'excavator_two';
	}
	elseif (!$level['excavator_three']) {
		$field = 'excavator_three';
	}
	elseif (!$level['excavator_four']) {
		$field = 'excavator_four';
	}
	else {
		echo "\tNo Room to add user $user to Level: [ " . $level['uid'] . " ]  L-" . $level['catalog_id'] . "\n";
		return false;
	}

	$sql = "UPDATE `level` SET `$field`='$clean_user' WHERE `uid`='$uid'";
	$db_results = Dba::write($sql);

	return $field;

} //excavator_fix

function record_level_fix($record_uid,$level_uid) { 

	$level_uid = Dba::escape($level_uid);
	$record_uid = Dba::escape($record_uid); 

	$sql = "UPDATE `record` SET `level`='$level_uid' WHERE `uid`='$record_uid'";
	$db_results = Dba::write($sql); 

	echo "\tSet Record UID [ $record_uid ] to Level UID [ $level_uid ]\n";

	return true; 

} //record_level_fix

// Record krotovina fix
function record_krotovina_fix($record_uid,$krotovina_uid) { 

	$record_uid = Dba::escape($record_uid);
	$krotovina_uid = Dba::escape($krotovina_uid); 

	$sql = "UPDATE `record` SET `krotovina`='$krotovina_uid' WHERE `uid`='$record_uid'";
	$db_results = Dba::write($sql); 

	echo "\tUpdated Record [ $record_uid ] set Krotovina UID to $krotovina_uid\n";

	return true; 

} // record_krotovina_fix

// Record feature fix
function record_feature_fix($record_uid,$feature_uid) { 

	$record_uid = Dba::escape($record_uid);
	$feature_uid = Dba::escape($feature_uid); 

	$sql = "UPDATE `record` SET `feature`='$feature_uid' WHERE `uid`='$record_uid'";
	$db_results = Dba::write($sql); 

	echo "\tUpdated Record [ $record_uid ] set Feature UID to $feature_uid\n";

	return true; 

} // record_featre_fix

// Record notes fix
function record_notes_fix($record_uid,$notes,$feature) { 

	$record_uid = Dba::escape($record_uid); 
	$notes = $notes . " ::Migration 2014:: Feature was $feature ::End::";
	$notes = Dba::escape($notes);

	$sql = "UPDATE `record` SET `notes`='$notes' WHERE `uid`='$record_uid'";
	$db_results = Dba::write($sql); 

	return true; 

} // record_notes_fix


?>
