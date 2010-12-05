<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_MiniLogTest extends BaseLogParserTestCase {  
  public function testMiniLog() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."mini.log");
    $this->assertEquals("09/29/2010 - 19:05:47", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    $this->assertEquals($countOfLines, count(explode("\n", $log->getScrubbedLog()))-1, "count scrubbed lines == count orig lines");
    $this->assertEquals(9, count($log->getStats()), "number of players, should exclude console");
    
    foreach($log->getStats() as $stat) {
      if($stat->getSteamid() == "STEAM_0:0:6845279") {
        //verify numbers for "Target"
        $this->assertEquals("Blue", $stat->getTeam(), "Target should be on team Blue");
        $this->assertEquals(4, $stat->getKills(), "target's kills");
        $this->assertEquals(1, $stat->getDominations(), "target's dominations");
        $this->assertEquals(1, $stat->getDeaths(), "target's deaths");
      } else if($stat->getSteamid() == "STEAM_0:1:16481274") {
        //verify numbers for "Barncow"
        $this->assertEquals(3, $stat->getDeaths(), "barncow's deaths");
        $this->assertEquals(1, $stat->getDestroyedobjects(), "barncow's destroyed objects");
        $this->assertEquals(1, $stat->getUbers(), "barncow's ubers");
      } else if($stat->getSteamid() == "STEAM_0:0:8581157") {
        //verify numbers for "Cres"
        $this->assertEquals(1, $stat->getAssists(), "cres' assists");
      } else if($stat->getSteamid() == "STEAM_0:1:9852193") {
        //verify numbers for "Ctrl+f Muffin!"
        $this->assertEquals(3, $stat->getDeaths(), "Ctrl+f Muffin!'s deaths");
        $this->assertEquals(1, $stat->getTimesDominated(), "Ctrl+f Muffin!'s times dominated");
        $this->assertEquals(1, $stat->getRevenges(), "Ctrl+f Muffin!'s revenges");
        $this->assertEquals(1, $stat->getBuiltobjects(), "Ctrl+f Muffin!'s built objects");
        $this->assertEquals(1, $stat->getKills(), "Ctrl+f Muffin!'s kills");
      } else if($stat->getSteamid() == "STEAM_0:0:11710749") {
        //verify numbers for "perl"
        $this->assertEquals(0, $stat->getDestroyedobjects(), "perl's destroyed objects - should be zero since do not want to count own destructions");
        $this->assertEquals(1, $stat->getExtinguishes(), "perl's extinguishes");
      } else if($stat->getSteamid() == "STEAM_0:1:23384772") {
        //verify numbers for "ǤooB/ǤooB's name is brandon too!!"
        $this->assertEquals("ǤooB's name is brandon too!!", $stat->getName(), "Goob's most recent name is kept");
        $this->assertEquals(null, $stat->getTeam(), "Goob should be on null team");
      }
    }
  }
}
