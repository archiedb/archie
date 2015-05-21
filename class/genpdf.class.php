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

  public function ticket_57x32mm ($record,$filename,$update_record) { 

    // Figure out what to do about MASL, order is
    // Level Closed -> [use start/stop]
    // Level Open, [use start/NO DATA]
    if ($record->level->closed) {
      $masl = $record->level->elv_center_start . '-' . $record->level->elv_center_finish;
    }
    else {
      $masl = $record->level->elv_center_start . '-NO DATA';
    }

    $pdf = new FPDF();
    $pdf->AddPage('L',array(57,32));

    $pdf->SetFont('Times','B');
    $pdf->SetFontSize('8');
    $pdf->Text('1','4','Catalog #:' . $record->catalog_id);
    $pdf->Text('33','4','Site:'. $record->site->name);
    $pdf->Text('1','8','Proj:' . $record->site->project);
    $pdf->Text('33','8','Acc#:' . $record->accession);
    $pdf->Text('1','12','Date:'.date('m/d/Y',$record->created));
    $pdf->Text('33','12','Unit:'. $record->level->unit);
    $pdf->Text('1','16','Name:' . $record->user->name); 
    $pdf->Text('33','16','Level:'. $record->level->catalog_id);
    $pdf->Text('1','20','Item(s):' . $record->material->name);
    $pdf->Text('33','20','N = '. $record->quanity);
    $pdf->Text('3','24',' ('. $record->classification->name . ')');
    $pdf->Text('1','28','MASL:' . $masl);

    $pdf->Output($filename);

    return true; 

  } // ticket_57x32mm

  public function ticket_88x25mm ($record,$filename) { 

    $pdf = new FPDF();
    $pdf->AddPage('L',array('88.9','25.4'));

    $feat_krot = $record->feature->uid ? $record->feature->record : $record->krotovina->record;

    // We need the QRcode filename here
    $qrcode = new Content($record->uid,'qrcode');

    // There are some edge cases where the QRCode might not exist, check and re-gen if needed
    if (!is_file($qrcode->filename)) {
     Content::write_qrcode($record->uid,$qrcode->filename,true);
    }

    $pdf->Image($qrcode->filename,'0','0','25.4','25.4');
    $pdf->SetFont('Times','B');
    $pdf->SetFontSize('8');
    $pdf->Text('25','4','SITE:' . $record->site->name);
    $pdf->Text('52','4','UNIT:' . $record->level->unit);
    $pdf->Text('25','8','LVL:' . $record->level->record);
    $pdf->Text('52','8','QUAD:' . $record->level->quad->name);
    $pdf->Text('25','12','MAT:' . $record->material->name);
    $pdf->Text('52','12','CLASS:' . $record->classification->name);
    $pdf->Text('25','16','L.U.:' . $record->lsg_unit->name);
    $pdf->Text('52','16','FEAT/KROT:' . $feat_krot);
    $pdf->Text('25','20','CAT#:' . $record->catalog_id);
    $pdf->Text('52','20','RN:' . $record->station_index);
    $pdf->Text('25','24',date('d-M-Y',$record->created));
    $pdf->Text('52','24','USER:' . $record->user->username);
    $pdf->Output($filename);

    return true; 

  } // ticket_88x25mm

} // end class level
?>