<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_MiniLogTest extends sfPHPUnitBaseTestCase {
  protected $logParser;
  protected $LFIXDIR;

  protected function _start() {
    $this->LFIXDIR = sfConfig::get('sf_test_dir')."/fixtures/LogParser/";
    $this->logParser = new LogParser();
  }
  
  protected function _end() {
    unset($this->logParser);
  }
  
  public function testMiniLog() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."mini.log");
    $this->assertEquals("09/29/2010 - 19:05:47", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    $this->assertEquals($countOfLines, count(explode("\n", $log->getScrubbedLog()))-1, "count scrubbed lines == count orig lines");
  }
}
