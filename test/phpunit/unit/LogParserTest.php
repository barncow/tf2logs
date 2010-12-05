<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_LogParserTest extends BaseLogParserTestCase {
  /**
  * @expectedException LogFileNotFoundException
  */
  public function testGetNotExistingLogFile() {	
    $this->logParser->getRawLogFile($this->LFIXDIR."asodijfsfj.log");
  }
  
  public function testGetExistingLogFile() {	
    $this->assertNotEmpty($this->logParser->getRawLogFile($this->LFIXDIR."blah.log"));
  }
  
  /**
  * @expectedException CorruptLogLineException
  */
  public function testCorruptFileThrowsCorruptLogLineException() {
    $this->logParser->parseLogFile($this->LFIXDIR."blah.log");
  }
}
