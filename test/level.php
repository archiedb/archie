<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
define('NO_LOG',1); 
define('CLI',1); 
require_once $prefix . '/class/init.php'; 
require_once $prefix . '/lib/enhancetest/EnhanceTestFramework.php';

class LevelClassTests extends \Enhance\TestFixture {

  public function setUp() { 


  } 

  /* Test Invalid Values */
  public function invalidUnit()       { $this->runFalseCreate('unit');  } // invalid unit
  public function invalidLevel()      { $this->runFalseCreate('level'); } // invalid level
  public function invalidLU()         { $this->runFalseCreate('lsg_unit'); } // invalid lsg_unit
  public function invalidEasting()    { $this->runFalseCreate('easting'); } // invalid easting
  public function invalidNorthing()   { $this->runFalseCreate('northing'); } // invalid northing

  /* Test Valid Record Creation */
  public function validCreate() {
    
    $input = $this->fillInput(); 
    \Enhance\Assert::isTrue(Level::create($input)); 

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
    $input['lsg_unit'] = '1';
    $input['northing'] = '111.111';
    $input['easting'] = '111.111';

    return $input;

  } // fillInput

} // LevelClassTests 
?>
