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
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_withGarbageAtEnd.log", 1);
    
    $this->assertEquals(895, count(explode("\n", $log->getLogFile()->getLogData()))-1, "count scrubbed lines stops when game appears over");
    
    foreach($log->getStats() as $stat) {
      $this->assertNotNull($stat->getTeam(), $stat->getPlayer()->getSteamid()." team is not null");
    }
  }
  
  public function testFull_1123dwidgranary() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_1123dwidgranary.log", 1);
    $this->assertEquals(1, $log->getBluescore(), "blue score of 1123dwidgranary");
    $this->assertEquals(5, $log->getRedscore(), "red score of 1123dwidgranary");
  }
  
  public function testFull_ctfdoublecross() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_ctfdoublecross.log", 1);
    $this->assertEquals(7, $log->getBluescore(), "blue score of doublecross");
    $this->assertEquals(2, $log->getRedscore(), "red score of doublecross");
  }
  
  public function testFull_plupward() {
    //note, this file was changed from original to make sure at the end the teams are on opposite teams from the beginning.
    //this is to ensure that team switching is being found.
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_plupward.log", 1);
    $this->assertEquals(4, $log->getBluescore(), "blue score of plupward");
    $this->assertEquals(0, $log->getRedscore(), "red score of plupward");
  }
}
