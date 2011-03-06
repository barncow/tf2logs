<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_NoTourneyTest extends BaseLogParserTestCase {    
  public function testNoTourneyLog() {
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."minilog_no_tourney.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    //test verifies that if a log file has no tournament mode, that the file is re-parsed and is considered all one round.
    foreach($log['Stats'] as $stat) {
      $this->assertNotNull($stat['team'], $stat['Player']['steamid']." team is not null");
      if($stat['Player']['steamid'] == "STEAM_0:0:6845279") {
        //verify numbers for "Target" - should have more than the minilog version.
        $this->assertEquals(5, $stat['kills'], "target's kills");
        break;
      }
    }
  }
}
