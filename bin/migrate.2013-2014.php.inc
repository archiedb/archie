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
	$levels[$row['uid']] = $row;
}

foreach ($records as $record) { 

	// If no level defined, skip
	if (!$record['level']) { 
		echo "Record: #" . $record['catalog_id'] . " [ " . $record['uid'] . " ] has no level skipping \n";
		continue;
	}

	// See if there's a level of record['level'] with the same quad/unit
	if (isset($levels[$record['level']])) { 
		// Now make sure it's really really the same thing
		if ($record['quad'] == $levels[$record['level']]['quad'] AND $record['unit'] == $levels[$record['level']]['unit']) {
			// Ok ok it is the same, move on
			echo "Record: #" . $record['catalog_id'] . " with level L-" . $levels[$record['level']]['catalog_id'] . " found checking excavators\n";
			$excavator = excavator_fix($record['user'],$levels[$record['level']]);
			if (!$excavator) { continue; }
			$levels[$record['level']][$excavator] = $record['user'];
			echo "User: " . $record['user'] . " Added as $excavator ON level L-" . $levels[$record['level']]['catalog_id'] . "\n";
		}
		else { 
			echo "Record: #" . $record['catalog_id'] . " doesn't match level L-" . $levels[$recdord['level']]['catalog_id'] . " searching...\n";
			$sql = "SELECT * FROM `level` WHERE `quad`='" . $record['quad'] . "' AND `unit`='" . $record['unit'] . "' AND `catalog_id`='" . $record['level'] . "'";
			$db_results = Dba::read($sql); 
			$row = Dba::fetch_assoc($db_results)
			if (!$row['uid']) { 
				$level_uid = migration_create_level($record['user'],$record['level'],$record['unit'],$record['quad']);
				echo "Level Created: [ $level_uid ] - L-" . $record['level'];
				$level = array('catalog_id'=>$record['level'],'quad'=>$record['quad'],'unit'=>$record['unit'],'uid'=>$level_uid,'excavator_one'=>$record['user']);
				$levels[$level_uid] = $level;
			}
		}
	} // if level is set
	// No level found
	else { 
		echo "Record: #"  . $record['catalog_id'] . " doesn't match any levels, creating a new one\n";
		$level_uid = migration_create_level($record['user'],$record['level'],$record['unit'],$record['quad']);
		echo "Level Created: [ $level_uid ] - L-" . $record['level'];
		$level = array('catalog_id'=>$record['level'],'quad'=>$record['quad'],'unit'=>$record['unit'],'uid'=>$level_uid);
		$levels[$level_uid] = $level;
	}

} // foreach records

// Hack'n'slash doesn't follow normal rules because this is a "shit its too late garbage in guess garbage is coming out" type deal
function migration_create_level($user,$catalog_id,$unit,$quad) {

	$user = Dba::escape($user);
	$catalog_id = Dba::escape($catalog_id);
	$unit = Dba::escape($unit);
	$quad = Dba::escape($quad);
	$sql = "INSERT INTO `level` (`user`,`catalog_id`,`unit`,`quad`,`created`,`excavator_one`) VALUES ('$user','$catalog_id','$unit','$quad')";
	$db_results = Dba::write($sql);

	$insert_id = Dba::insert_id();

	return $insert_id;

} // migration_create_level

// Make sure this user is listed on the excavators list, if it's not already full
function excavator_fix($user,$level) {

	$uid = Dba::escape($level['uid']);
	$clean_user = Dba::escape($user);

	// See if this user is listed already
	if ($level['excavator_one'] == $user OR $level['excavator_two'] == $user OR $level['excavator_three'] == $user OR $level['excavator_four'] == $user) {
		// return false nothing done/fixed
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
		echo "NO ROOM TO ADD $user TO Level: [ " . $level['uid'] . " ]  L-" . $level['catalog_id'] . "\n";
		return false;
	}

	$sql = "UPDATE `level` SET `$field`='$clean_user' WHERE `uid`='$uid'";
	$db_results = Dba::write($sql);

	return $field;

} //excavator_fix

?>
