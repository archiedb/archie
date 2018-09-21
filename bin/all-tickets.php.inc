<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:

define('CLI',true); 
define('NO_LOG',true); 
define('OUTDATED_DATABASE_OK','1');
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
require_once $prefix . '/class/init.php';
\UI\sess::set_user(User::get_from_username('cron')); 

// First build the giant list of records, and then create a single
// Monster PDF with every single ticket for the site in question. 

$classsql = "SELECT `uid`,`name` FROM `classification` ORDER BY `name` DESC";
$db_results = Dba::read($classsql);

$records = array();
$i = 0;

// Sort by Classification first
while ($class_row = Dba::fetch_assoc($db_results)) {
  $classrecsql = "SELECT `record`.`uid`,`material`.`name`,`level`.`unit`,`level`.`quad`,`level`.`catalog_id` FROM " . 
            " `record` LEFT JOIN `level` ON `level`.`uid`=`record`.`level` LEFT JOIN `material` ON `material`.`uid`=`record`.`material` WHERE `classification`=" .$class_row['uid'];
  $rdb_results = Dba::read($classrecsql,array($class_row['uid']));
  while ($record_row = Dba::fetch_assoc($rdb_results)) {
    $records[$class_row['name']][$record_row['name']][$record_row['unit']][$record_row['quad']][$record_row['catalog_id']][] = $record_row['uid'];
  }
}

// Foreach Classifications
foreach ($records as $class=>$classdata) {
  krsort($records[$class]);
  foreach ($records[$class] as $material=>$materialdata) {
    krsort($records[$class][$material]);
    foreach ($records[$class][$material] as $unit=>$unitdata) {
      krsort($records[$class][$material][$unit]);
      foreach ($records[$class][$material][$unit] as $quad=>$quaddata) {
        krsort($records[$class][$material][$unit][$quad]);
        foreach ($records[$class][$material][$unit][$quad] as $level=>$leveldata) {
          arsort($records[$class][$material][$unit][$quad][$level]);
        }
      }
    }
  }
}

$pdf = new FPDF();
foreach ($records as $class=>$classdata) {
  foreach ($records[$class] as $material=>$materialdata) {
    foreach ($records[$class][$material] as $unit=>$unitdata) {
      foreach ($records[$class][$material][$unit] as $quad=>$quaddata) {
        foreach ($records[$class][$material][$unit][$quad] as $level=>$leveldata) {
          foreach ($leveldata as $r_uid) {
  $pdf->AddPage('L',array('88.9','25.4'));
  $record = new Record($r_uid);
  $feat_krot = $record->feature->uid ? $record->feature->record : $record->krotovina->record;

    $quad = empty($record->level->quad->name) ? '' : '-' . $record->level->quad->name;
    $nor = empty($record->northing) ? '' : 'N' . $record->northing . ' ';
    $est = empty($record->easting) ? '' : 'E' . $record->easting . ' ';
    $elv = empty($record->elevation) ? '' : 'Z' . $record->elevation;

    //$pdf->Image($qrcode->filename,'0','0','24.4','24.4');
    $pdf->SetFont('Times','B');
    $pdf->SetFontSize('8');
    $pdf->Text('14','4.25','SITE:' . $record->site->name);
    $pdf->Text('48','4.25','UNIT-QUAD:' . $record->level->unit->name . $quad);
    $pdf->Text('14','7.5','LVL:' . $record->level->record );
    $pdf->Text('48','7.5','QUANTITY:' . $record->quanity);
    $pdf->Text('14','10.75','MAT:' . $record->material->name);
    $pdf->Text('48','10.75','CLASS:' . $record->classification->name);
    $pdf->Text('14','14','L.U.:' . $record->lsg_unit->name);
    $pdf->Text('48','14','FEAT/KROT:' . $feat_krot);
    $pdf->Text('14','17.25','CAT#:' . $record->catalog_id);
    $pdf->Text('48','17.25','RN:' . $record->station_index);
    $pdf->Text('14','20.5',date('d-M-Y',$record->created));
    $pdf->Text('48','20.5','TECH:' .  $record->user->username);
          }
        }
      }
    }
  }
}

$pdf->Output('/tmp/bar.pdf');

?>
