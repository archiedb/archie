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

  public function validateRecordInput() {

    $input = $this->invalidRecordValues(); 
    \Enhance\Assert::isFalse(Record::create($input)); 
    
  }

  private function invalidRecordValues() { 
      
      $input['user'] = 'ZZZ'; 

      return $input; 

  }

} 
?>
