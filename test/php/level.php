<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../../");
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

class LevelClassTests extends \Enhance\TestFixture {

  public function setUp() { 


  } 

  /* Test Invalid Values */
  public function Level_invalidUnit()       { $this->runFalseCreate('unit');  } // invalid unit
  public function Level_invalidLevel()      { $this->runFalseCreate('level'); } // invalid level
  public function Level_invalidLU()         { $this->runFalseCreate('lsg_unit'); } // invalid lsg_unit
  public function Level_invalidEasting()    { $this->runFalseCreate('easting'); } // invalid easting
  public function Level_invalidNorthing()   { $this->runFalseCreate('northing'); } // invalid northing

  /* Test Valid Record Creation */
  public function validCreate() {
    
    $input = $this->fillInput(); 
    $results = Level::create($input);
    if ($results) { $results = true; }
    \Enhance\Assert::isTrue($results); 

  }

  private function runFalseCreate($field) {

    $input = $this->fillInput(); 
    $data = TestData::level($field,false); 

    foreach ($data as $value) { 
      $input[$field] = $value; 
      \Enhance\Assert::isFalse(Level::create($input));
    }

    return true; 

  } // runFalseCreate

  // Clear the levels table, needed for creation tests since they
  // would start erroring on duplicate values
  private function clearLevels() {


  } // clearLevels

  /** 
   * fillInput
   */
  private function fillInput() { 

    // Create a record
    $input = array(); 

    $input['unit'] = 'A';
    $input['level'] = '1';
    $input['quad'] = '1';
    $input['lsg_unit'] = '2';
    $input['catalog_id'] = '1';
    $input['northing'] = '111.111';
    $input['easting'] = '111.111';
    $input['excavator_one'] = '1';
    $input['excavator_two'] = '2';
    $input['excavator_three'] = '3';
    $input['excavator_four'] = '4';
    $input['elv_nw_start'] = '111.111';
    $input['elv_nw_finish'] = '111.110';
    $input['elv_ne_start'] = '111.111';
    $input['elv_ne_finish'] = '111.110';
    $input['elv_sw_start'] = '111.111';
    $input['elv_sw_finish'] = '111.110';
    $input['elv_se_start'] = '111.111';
    $input['elv_se_finish'] = '111.110';
    $input['elv_center_start'] = '111.111';
    $input['elv_center_finish'] = '111.110';
    $input['user'] = '1';
    $input['uid'] = '1';

    return $input;

  } // fillInput

} // LevelClassTests 
?>
