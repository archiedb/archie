<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Genpdf { 

  public $type;

	// Constructor takes a uid
	public function __construct($type) { 

    //FIXME: validate this
    $this->type = $type;

		return true; 

	} // constructor

  public function ticket_57x32mm ($record,$filename) { 

    // Figure out what to do about MASL, order is
    // Level Closed -> [use start/stop]
    // Level Open, [use start/NO DATA]
    if ($record->elevation > 0) { 
      $masl = $record->elevation;
    }
    else { 
      $elv_finish = ($record->level->elv_center_finish > 0) ? $record->level->elv_center_finish : 'NO DATA';
      $elv_start = ($record->level->elv_center_start > 0) ? $record->level->elv_center_start : 'NO DATA';
      $masl = $elv_start.'-'.$elv_finish;
    }

    $site_abv = substr(preg_replace('/\b(\w)\w*\W*/', '\1', $record->site->description),0,3);

    $pdf = new FPDF();
    $pdf->AddPage('L',array(57,32));

    $pdf->SetFontSize('8.5');
    $pdf->SetFont('Times','B');
    $pdf->Text('9','4',$record->site->accession . ' - ' . $site_abv . ' - ' . $record->level->unit->name . '/' . $record->level->quad->name . ' - ' . $record->level->catalog_id . ' - ' . $record->excavation_count);
    $pdf->Text('1','8','Catalog #:' . $record->catalog_id);
    $pdf->Text('33','8','Site:'. $record->site->name);
    $pdf->Text('1','12','Proj:' . $record->site->project);
    $pdf->Text('33','12','Acc#:' . $record->site->accession);
    $pdf->Text('1','16','Date:'.date('m/d/Y',$record->created));
    $pdf->Text('33','16','Unit:'. $record->level->unit->name);
    $pdf->Text('1','20','Name:' . $record->user->name); 
    $pdf->Text('33','20','Level:'. $record->level->catalog_id);
    $pdf->Text('1','23','Item(s):' . $record->material->name);
    $pdf->Text('33','23','N = '. $record->quanity);
    $pdf->Text('3','27',' ('. $record->classification->name . ')');
    $pdf->Text('1','31','Elv:' . $masl);
  

    $pdf->Output($filename);

    return true; 

  } // ticket_57x32mm

  public function ticket_88x25mm ($record,$filename) { 

    $pdf = new FPDF();
    $pdf->AddPage('L',array('88.9','25.4'));

    $feat_krot = $record->feature->uid ? $record->feature->record : $record->krotovina->record;

    // We need the QRcode filename here
    $qrcode = new Content($record->uid,'qrcode','record');

    // If there is no filename on this qrcode re-do it
    if (empty($qrcode->filename)) {
      Content::write($record->uid,'qrcode');
      $qrcode->refresh();
    }

    // There are some edge cases where the QRCode might not exist, check and re-gen if needed
    if (!is_file($qrcode->filename)) {
     Content::write_qrcode($record->uid,$qrcode->filename,true);
    }

    // Verify permissions
    if (!is_readable($qrcode->filename) OR !is_writeable(dirname($filename))) {
      Event::error('genpdf::ticket_88x25mm','Error creating ticket :: ' . dirname($filename) . ' is not writeable and or ' . $qrcode->filename . ' is not readable');
      return false;
    }

    $quad = empty($record->level->quad->name) ? '' : '-' . $record->level->quad->name;
    $nor = empty($record->northing) ? '' : 'N' . $record->northing . ' ';
    $est = empty($record->easting) ? '' : 'E' . $record->easting . ' ';
    $elv = empty($record->elevation) ? '' : 'Z' . $record->elevation;

    $pdf->Image($qrcode->filename,'0','0','24.4','24.4');
    $pdf->SetFont('Times','B');
    $pdf->SetFontSize('8');
    $pdf->Text('25','3.5','SITE:' . $record->site->name);
    $pdf->Text('51','3.5','UNIT-QUAD:' . $record->level->unit->name . $quad);
    $pdf->Text('25','7','LVL:' . $record->level->record );
    $pdf->Text('51','7','QUANITY:' . $record->quanity);
    $pdf->Text('25','10.5','MAT:' . $record->material->name);
    $pdf->Text('51','10.5','CLASS:' . $record->classification->name);
    $pdf->Text('25','14','L.U.:' . $record->lsg_unit->name);
    $pdf->Text('51','14','FEAT/KROT:' . $feat_krot);
    $pdf->Text('25','17.5','CAT#:' . $record->catalog_id);
    $pdf->Text('51','17.5','RN:' . $record->station_index);
    $pdf->Text('25','21',date('d-M-Y',$record->created));
    $pdf->Text('51','21','TECH:' .  $record->user->username);
    $pdf->Text('25','24.5','LOC:' . $nor . $est . $elv);
    $pdf->Output($filename);

    return true; 

  } // ticket_88x25mm

  /** 
   * feature_report
   * Multi-page document for Features
   */
  public function feature_report(&$feature,$filename) {

    Err::clear();
    $current_page = 1;
    $spatialdata = SpatialData::get_record_data($feature->uid,'feature');
    $records = $feature->get_records();
    $total_pages = ceil(2+(count($records)/55)+(count($spatialdata)/55));
    // Run some tests on the feature image
    $featureimage = new Content($feature->image,'image');

    if (!is_readable($featureimage->filename)) {
      Event::error('Feature-PDF','Feature Image ' . $featureimage->filename . ' is not readable');
      Err::add('feature_image','Feature Image is not readable or not found');
    }
    if (!is_writeable(Config::get('prefix') . '/lib/cache') OR !is_readable(Config::get('prefix') . '/lib/cache')) {
      Event::error('Feature-PDF','Cache directory unwriteable, unable to resize image');
      Err::add('feature_image','Cache directory unwriteable, unable to resize image');
    }

    if (Err::occurred()) {
      Err::display('feature_image');
      require \UI\template('/footer');
      exit;
    }

    $start_time = time();

    $pdf = new FPDF();
    $pdf->AddPage('P','A4');
    $pdf->SetTitle('Feature-' . $feature->site->name . '-' . $feature->record . '-Form');
    $pdf->SetSubject('Feature Form');
    $pdf->SetFont('Times');
    $pdf->SetFontSize('10');
    $pdf->Text('200','295',$current_page . '/' . $total_pages);
    $pdf->Text('140','295',' Generated ' . date('Y-M-d H:i',$start_time));
    $pdf->Text('3','295',$feature->site->name . ' ' . $feature->record . ' FORM');

    //FIXME: Need to create primary image concept for Features and Krotovina and load it here
    if (!$feature->updated) { $feature->updated = $feature->created; }

    // Header 
    $pdf->SetFont('Times','B');
    $pdf->SetFontSize('12');
    $pdf->Text('3','5',$feature->site->name . ' ' . $feature->record . ' FEATURE EXCAVATION FORM');
    $pdf->Text('3','10',$feature->site->description);
    $pdf->Text('155','5','Started: ' . date('d-M-Y',$feature->created));
    $pdf->Text('155','10','Updated: ' . date('d-M-Y',$feature->updated));
    $pdf->Line('0','12','220','12');
   
    $resized_file = Config::get('prefix') . '/' . \UI\resize($featureimage->filename,array('w'=>'980','h'=>'803','canvas-color'=>'#ffffff'));
    $pdf->Image($resized_file,'10','14','190','155');

    // Answers to the questions
    $pdf->SetFontSize('15');
    $pdf->Text('5','170','Questions:');
    $pdf->SetFontSize('10');
    $pdf->Text('5','177','Q) How is the feature differentiated from the surrounding sediments?');
    $pdf->Text('10','182','What are its defining characteristics?');
    $pdf->SetFont('Times');
    $pdf->SetX('0');
    $pdf->SetY('187');
    $pdf->Write('4',$feature->description);
    // Figure out where we are, based on length of response
    $start_y = $pdf->GetY();
    $pdf->SetFont('Times','B');
    $pdf->Text('5',$start_y+12,'Q) Other Notes?');
    $pdf->SetX('0');
    $pdf->SetY($start_y+14);
    $pdf->SetFont('Times');
    $pdf->Write('4',$feature->keywords);

    // Show the graphs, this is a little dangerous, and might be slow
    $plotcmd = Config::get('prefix') . '/bin/build-feature-plots ' . escapeshellarg($feature->uid);
    $output = exec($plotcmd);

    $plot = new Content($feature->uid,'scatterplot','feature');

    $pdf->AddPage();
    $pdf->SetFontSize('18');
    $pdf->SetFont('Times');
    $current_page++;

    $pdf->Text('80','13','Feature Points & Contained Records');
    
    # Make sure we have all 4 plots
    if (count($plot->filename) == 4) {


      $pdf->image($plot->filename['EstXNor'],'2','15','104','104');
      $pdf->image($plot->filename['EstXElv'],'105','15','104','104');
      $pdf->image($plot->filename['NorXElv'],'2','125','104','104');
      $pdf->image($plot->filename['3D'],'105','125','104','104');


    } // end if 4 files found
    // Tell em its empty
    else {

      $pdf->SetFontSize('25');
      $pdf->Text('35','135','No scatterplots available for this feature');
      $pdf->SetFontSize('10');

    }

    $pdf->Image(Config::get('prefix') . '/images/archie_legend.png','60','240','100','27');
    $pdf->SetFontSize('10');
    $pdf->Text('200','295',$current_page. '/' . $total_pages);
    $pdf->Text('140','295'," Generated " . date("Y-M-d H:i",$start_time));
    $pdf->Text('3','295',$feature->site->name . ' ' . $feature->record . ' FORM');

    # Write out the coordinates
    while (count($spatialdata)) { 
      
      $pdf->AddPage();
      $pdf->SetFontSize('15');
      $pdf->SetFont('Times','B');
      $pdf->Text(2,6,'Feature Spatial Information');
      $pdf->SetFont('Times');
      $pdf->SetFontSize('10');
      $current_page++;
      $pdf->Text('200','295',$current_page . '/' . $total_pages);
      $pdf->Text('140','295',' Generated ' . date('Y-M-d H:i',$start_time));
      $pdf->Text('3','295',$feature->site->name . ' ' . $feature->record . ' FORM');

      $row = 0;
      $line_count = 0;
      $start_y = 20;
      $record_count = count($spatialdata);

      foreach ($spatialdata as $data) {
        # If we've reached the end, trim and reset
        if ($line_count == 55) {
          $start_y = 20;
          $spatialdata = array_slice($spatialdata,55);
          break;
        }

        $line_count++;

        # First and 59th (2nd row) lines and we set the table
        if ($line_count == 1) {
          $pdf->setFont('Times','B');
          $pdf->Line(2,'10',202,'10');
          $pdf->Text(5,14,'Station Index (RN)');
          $pdf->Text(50,14,'Northing');
          $pdf->Text(74,14,'Easting');
          $pdf->Text(95,14,'Elevation');
          $pdf->Text(117,14,'Note');
          $pdf->Line(2,'16',202,'16');

          $pdf->SetFontSize('10');
          $pdf->SetFont('Times');

          $line_end = ($record_count > 55) ? (55 *5)+16 : ($record_count*5)+16;
          $pdf->Line(2,'10',2,$line_end);
          $pdf->Line(46,'10',46,$line_end);
          $pdf->Line(71,'10',71,$line_end);
          $pdf->Line(92,'10',92,$line_end);
          $pdf->Line(115,'10',115,$line_end);
          $pdf->Line(202,10,202,$line_end);

        } // end initial row

        $spatialdata = new Spatialdata($data['uid']);
        $pdf->Text(3,$start_y,$spatialdata->station_index);
        $pdf->Text(46,$start_y,$spatialdata->northing);
        $pdf->Text(72,$start_y,$spatialdata->easting);
        $pdf->Text(93,$start_y,$spatialdata->elevation);
        $pdf->Text(116,$start_y,substr($spatialdata->note,0,30));
        $pdf->Line(2,$start_y+1,202,$start_y+1);
        $start_y += 5;

      } // end foreach spatialdata
      if ($line_count < 55) { break; }
    } // end while

    // Write out records
    while (count($records)) {
      $pdf->AddPage();
      $pdf->SetFontSize('15');
      $pdf->SetFont('Times','B');
      $pdf->Text(2,6,'Records within Feature');
      $pdf->SetFont('Times');
      $pdf->SetFontSize('10');
      $current_page++;
      $pdf->Text('200','295',$current_page. '/' . $total_pages);
      $pdf->Text('140','295',' Generated ' . date('Y-M-d H:i',$start_time));
      $pdf->Text('3','295',$feature->site->name . ' ' . $feature->record . ' FORM');
      $row = 0;
      $line_count = 0;
      $start_y = 20;
      $record_count = count($records);

      foreach ($records as $record_id) {
        # If we've reached the end, trim and reset
        if ($line_count == 55) {
          $start_y = 20;
          $records = array_slice($records,55);
          break;
        }
        $line_count++;
        # First and 59th (2nd row) lines and we set the table
        if ($line_count == 1) {
          $pdf->SetFont('Times','B');
          $pdf->Line(2,'10',202,'10');
          $pdf->Text(5,14,'Catalog');
          $pdf->Text(25,14,'RN');
          $pdf->Text(44,14,'Material');
          $pdf->Text(68,14,'Classification');
					$pdf->Text(102,14,'Northing');
          $pdf->Text(119,14,'Easting');
          $pdf->Text(135,14,'Elevation');
          $pdf->Text(155,14,'Quanity');
          $pdf->Text(179,14,'Entered By');
          $pdf->Line(2,'16',202,'16');

          # Itterate through the records
          $pdf->SetFontSize('10');
          $pdf->SetFont('Times');

          $line_end = ($record_count > 55) ? (55*5)+16 : ($record_count*5)+16;
          $pdf->Line(2,'10',2,$line_end);
          $pdf->Line(21,'10',21,$line_end);
          $pdf->Line(40,'10',40,$line_end);
          $pdf->Line(66,'10',66,$line_end);
          $pdf->Line(101,'10',101,$line_end);
          $pdf->Line(118,10,118,$line_end);
          $pdf->Line(133,10,133,$line_end);
          $pdf->Line(153,10,153,$line_end);
          $pdf->Line(177,10,177,$line_end);
          $pdf->Line(202,10,202,$line_end);
        }

        # Load and print record record
        $record = new Record($record_id);
        $pdf->Text(3,$start_y,$record->catalog_id);
        $pdf->Text(22,$start_y,$record->station_index);
        $pdf->Text(41,$start_y,$record->material->name);
        $pdf->Text(67,$start_y,$record->classification->name);
        $pdf->Text(102,$start_y,$record->northing);
        $pdf->Text(119,$start_y,$record->easting);
        $pdf->Text(134,$start_y,$record->elevation);
        $pdf->Text(154,$start_y,$record->quanity);
				$pdf->Text(178,$start_y,$record->user->username);
        $pdf->Line(2,$start_y+1,202 ,$start_y+1);
        $start_y += 5;

      } // end foreach
      if ($line_count < 55) { break; }

    } // end while records


    ob_end_clean();
    $pdf->Output();

    return true; 

  } // feature_report

} // end class level
?>
