<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_MiniLogTest extends BaseLogParserTestCase {  
  public function testMiniLog() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."mini.log");
    $this->assertEquals("09/29/2010 - 19:05:47", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    $this->assertEquals($countOfLines, count(explode("\n", $log->getScrubbedLog()))-1, "count scrubbed lines == count orig lines");
    $this->assertEquals(2, count($log->getStats()), "number of players, should exclude console");
    
    foreach($log->getStats() as $stat) {
      if($stat->getSteamid() == "STEAM_0:0:6845279") {
        //verify numbers for "Target"
        $this->assertEquals("Blue", $stat->getTeam(), "Target should be on team Blue");
        $this->assertEquals(2, $stat->getKills(), "target's kills");
      } else if($stat->getSteamid() == "STEAM_0:1:16481274") {
        //verify numbers for "Barncow"
        $this->assertEquals(2, $stat->getDeaths(), "barncow's deaths");
      }
    }
  }
}
