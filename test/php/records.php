<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../../");
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

class RecordClassTests extends \Enhance\TestFixture {

  private static $rn = 0;

  public function setUp() { 


  } 

  /* Test Invalid Values */
  public function Record_invalidLevel()      { $this->runFalseCreate('level'); } // invalid level
  public function Record_invalidLU()         { $this->runFalseCreate('lsg_unit'); } // invalid lsg_unit
  public function Record_invalidRN()         { $this->runFalseCreate('station_index'); } // invalid RN
  public function Record_invalidWeight()     { $this->runFalseCreate('weight'); } // invalid weight
  public function Record_invalidHeight()     { $this->runFalseCreate('height'); } // invalid height
  public function Record_invalidWidth()      { $this->runFalseCreate('width'); } // invalid width
  public function Record_invalidEasting()    { $this->runFalseCreate('easting'); } // invalid easting
  public function Record_invalidElevation()  { $this->runFalseCreate('elevation'); } // invalid elevation
  public function Record_invalidNorthing()   { $this->runFalseCreate('northing'); } // invalid northing
  public function Record_invalidXRF()        { $this->runFalseCreate('xrf_matrix_index'); } // invalid xrf
  public function Record_invalidQuanity()    { $this->runFalseCreate('quanity'); } // invalid quanity

  /* Test Valid Record Creation */
  public function validCreate() {
    
    $retval = false; 
    $input = $this->fillInput(); 
    $results = Record::create($input);
    if ($results) { 
      // Make sure the record exists by pulling it back out
      $record = new Record($results);
      if ($record->uid > 0) { $retval = true; }
    } 
    \Enhance\Assert::isTrue($retval); 

  }

  private function runFalseCreate($field) {

    $input = $this->fillInput(); 
    $data = TestData::record($field,false); 

    foreach ($data as $value) { 
      $input[$field] = $value; 
      \Enhance\Assert::isFalse(Record::create($input));
    }

    return true; 

  } // runFalseCreate

  // Clear the records table, needed for creation tests since they
  // would start erroring on duplicate values
  private function clearRecords() {


  } // clearRecords

  /** 
   * fillInput
   */
  private function fillInput() { 

    // Create a record
    $input = array(); 

    $input['level'] = '1';
    $input['lsg_unit'] = '1';
    $input['northing'] = '111.111';
    $input['easting'] = '111.111';
    $input['elevation'] = '111.111';
    $input['xrf_matrix_index'] = '111';
    $input['weight'] = '11.111';
    $input['height'] = '11.111';
    $input['width'] = '11.111';
    $input['thickness'] = '11.111';
    $input['quanity'] = '1';
    $input['xrf_artifact_index'] = '111';
    $input['material'] = '1';
    $input['classification'] = '1';
    $input['notes'] = '1';
    $input['user'] = '1';
    $input['station_index'] = NULL;

    return $input;

  } // fillInput

} // RecordClassTests 
?>
