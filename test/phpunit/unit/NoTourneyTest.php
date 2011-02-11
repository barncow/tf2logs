<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_NoTourneyTest extends BaseLogParserTestCase {    
  public function testNoTourneyLog() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."minilog_no_tourney.log", 1);
    //test verifies that if a log file has no tournament mode, that the file is re-parsed and is considered all one round.
    foreach($log->getStats() as $stat) {
      $this->assertNotNull($stat->getTeam(), $stat->getPlayer()->getSteamid()." team is not null");
      if($stat->getPlayer()->getSteamid() == "STEAM_0:0:6845279") {
        //verify numbers for "Target" - should have more than the minilog version.
        $this->assertEquals(5, $stat->getKills(), "target's kills");
        break;
      }
    }
  }
}
