<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_FullLogTest extends sfPHPUnitBaseTestCase {
  protected $logParser;
  protected $LFIXDIR;

  protected function _start() {
    $this->LFIXDIR = sfConfig::get('sf_test_dir')."/fixtures/LogParser/";
    $this->logParser = new LogParser();
  }
  
  protected function _end() {
    unset($this->logParser);
  }
  
  public function testFull_withGarbageAtEnd() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_withGarbageAtEnd.log");
    
    $this->assertEquals(895, count(explode("\n", $log->getScrubbedLog()))-1, "count scrubbed lines stops when game appears over");
    
    foreach($log->getStats() as $stat) {
      $this->assertNotNull($stat->getTeam(), $stat->getSteamid()." team is not null");
    }
  }
}
