<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
define('NO_LOG',1); 
define('CLI',1); 
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

class RecordClassTests extends \Enhance\TestFixture {

  public function setUp() { 


  } 

  /* Test Invalid Values */
  public function invalidUnit()       { $this->runFalseCreate('unit');  } // invalid unit
  public function invalidLevel()      { $this->runFalseCreate('level'); } // invalid level
  public function invalidLU()         { $this->runFalseCreate('lsg_unit'); } // invalid lsg_unit
  public function invalidRN()         { $this->runFalseCreate('station_index'); } // invalid RN
  public function invalidWeight()     { $this->runFalseCreate('weight'); } // invalid weight
  public function invalidHeight()     { $this->runFalseCreate('height'); } // invalid height
  public function invalidWidth()      { $this->runFalseCreate('width'); } // invalid width
  public function invalidEasting()    { $this->runFalseCreate('easting'); } // invalid easting
  public function invalidElevation()  { $this->runFalseCreate('elevation'); } // invalid elevation
  public function invalidNorthing()   { $this->runFalseCreate('northing'); } // invalid northing
  public function invalidXRF()        { $this->runFalseCreate('xrf_matrix_index'); } // invalid xrf
  public function invalidQuanity()    { $this->runFalseCreate('quanity'); } // invalid quanity

  /* Test Valid Record Creation */
  public function validCreate() {
    
    $input = $this->fillInput(); 
    \Enhance\Assert::isTrue(Record::create($input)); 

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

    $input['unit'] = 'A';
    $input['level'] = '1';
    $input['quad'] = '1';
    $input['lsg_unit'] = '1';
    $input['station_index'] = '111';
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

    return $input;

  } // fillInput

} // RecordClassTests 
?>
