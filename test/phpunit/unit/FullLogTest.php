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
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_withGarbageAtEnd.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $logfile = Doctrine::getTable('LogFile')->findOneByLogId($logid);
    
    //893 instead of 894 because rcon line is removed.
    $this->assertEquals(893, count(explode("\n", $logfile->getLogData()))-1, "count scrubbed lines stops when game appears over");
    
    foreach($log['Stats'] as $stat) {
      $this->assertNotNull($stat['team'], $stat['Player']['steamid']." team is not null");
    }
  }
  
  public function testFull_1123dwidgranary() {
    //note - this log seems to start in the middle of a round. the first capture by red currently does not count until the "round_start"
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_1123dwidgranary.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $this->assertEquals(1, $log['bluescore'], "blue score of 1123dwidgranary");
    $this->assertEquals(4, $log['redscore'], "red score of 1123dwidgranary");
    
    foreach($log['Stats'] as $stat) {
      if($stat['name'] == "=OBL= Rubber Ducky") {
        $this->fail("Spectator found in granary log.");
      }
    }
  }
  
  public function testFull_ctfdoublecross() {
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_ctfdoublecross.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $this->assertEquals(7, $log['bluescore'], "blue score of doublecross");
    $this->assertEquals(2, $log['redscore'], "red score of doublecross");
  }
  
  public function testFull_plupward() {
    //note, this file was changed from original to make sure at the end the teams are on opposite teams from the beginning.
    //this is to ensure that team switching is being found.
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_plupward.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $this->assertEquals(4, $log['bluescore'], "blue score of plupward");
    $this->assertEquals(0, $log['redscore'], "red score of plupward");
  }
  
  public function testFull_kothviaduct() {
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_kothviaduct.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $this->assertEquals(4, $log['bluescore'], "blue score of kothviaduct");
    $this->assertEquals(0, $log['redscore'], "red score of kothviaduct");
  }
  
  /**
    The purpose of this test is to check that the logparser handles multiple halves correctly.
    The log should contain a game_over, then a round_start. The log should ignore everything between the two, except player teams.
    This log file also includes a bogus round_win from a reached time limit game_over.
    The log has been edited to give a player some kills that should let us know that kills were not counted between halves.
    The log has also been edited so that the players will also switch sides.
    
    KOTH and PL maps both depend on round_wins not proceeding right after a capture. This feature has been removed,
    and we will just suck up the extra round_win
  */
  public function testFull_badlands_2halves() {
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."full_badlands_2halves.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    
    $this->assertEquals(5, $log['redscore'], "red score");
    
    $this->assertEquals(0, $log['bluescore'], "blue score");
    
    foreach($log['Stats'] as $stat) {
      if($stat['Player']['steamid'] == "STEAM_0:1:16481274") {
        //barncow
        //halftime intermission has 3 kills, plus barncow was given two kills on either half, so should only have 2 kills here.
        $this->assertEquals(2, $stat['kills'], "barncow's kills");
        
        break;
      }
    }
  }
  
  /**
  * @expectedException NoDataInLogFileException
  */
  public function testFull_empty() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."full_empty.log", 1);
  }
}
