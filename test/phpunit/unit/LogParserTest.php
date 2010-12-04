<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_LogParserTest extends sfPHPUnitBaseTestCase
{
  protected $logParser;
  protected $LFIXDIR;
  
  public function _start() {
    $this->LFIXDIR = sfConfig::get('sf_test_dir')."/fixtures/LogParser/";
    $this->logParser = new LogParser();
  }
  
  public function _end() {
    unset($this->logParser);
  }
  
  /**
  * @expectedException LogFileNotFoundException
  */
  public function testGetNotExistingLogFile()
  {	
    $this->logParser->getRawLogFile($this->LFIXDIR."asodijfsfj.log");
  }
  
  public function testGetExistingLogFile()
  {	
    $this->assertNotEmpty($this->logParser->getRawLogFile($this->LFIXDIR."blah.log"));
  }
}
