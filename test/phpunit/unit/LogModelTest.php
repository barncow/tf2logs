<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_LogModelTest extends sfPHPUnitBaseTestCase
{
  public function testAddUniqueStatForPlayerInfo() {
   $pi1 = new PlayerInfo("myname", "steamid", "team");
   $pi2 = new PlayerInfo("mynewname", "mynewsteamid", "mynewteam");
   
   $log = new Log();
   $log->addUpdateUniqueStatFromPlayerInfo($pi1);
   $this->assertEquals(1, count($log->Stats), "insertion of first record should have 1 stat in log");
   
   $log->addUpdateUniqueStatFromPlayerInfo($pi1);
   $this->assertEquals(1, count($log->Stats), "insertion of first record, second time should have 1 stat in log");
   
   $log->addUpdateUniqueStatFromPlayerInfo($pi2);
   $this->assertEquals(2, count($log->Stats), "insertion of second record should have 2 stats in log");
  }
  
  public function testAddUniqueStatsForPlayerInfos() {
    $pi1 = new PlayerInfo("myname", "steamid", "team");
    $pi2 = new PlayerInfo("mynewname", "mynewsteamid", "mynewteam");
    $pi3 = new PlayerInfo("notonteam", "notonteamsid", null);

    $log = new Log();
    $log->addUpdateUniqueStatFromPlayerInfo($pi1);
    $log->addUpdateUniqueStatsFromPlayerInfos(array($pi1, $pi2));
    $this->assertEquals(2, count($log->Stats), "insertion of array with first inserted record should only have 2");
    
    $pi1->setName("blah");
    $pi2->setTeam("Blue");
    $log->addUpdateUniqueStatsFromPlayerInfos(array($pi1, $pi2));
    $this->assertEquals(2, count($log->Stats), "insertion of array with updated attrs should only have 2");
    foreach($log->Stats as $s) {
      if($s->equalsPlayerInfo($pi1)) {
        $this->assertEquals("blah", $s->getName(), "name of PI1 should be updated");
      } else if($s->equalsPlayerInfo($pi2)) {
        $this->assertEquals("Blue", $s->getTeam(), "team of PI2 should be updated");
      }
    }
    
    $log->addUpdateUniqueStatFromPlayerInfo($pi3);
    $this->assertEquals(2, count($log->Stats), "insertion of player with no team should not be added");
    
    $pi1->setName("pants");
    $pi1->setTeam(null);
    $log->addUpdateUniqueStatFromPlayerInfo($pi1);
    foreach($log->Stats as $s) {
      if($s->equalsPlayerInfo($pi1)) {
        $this->assertEquals("pants", $s->getName(), "name of player that switched to spec should be updated");
      }
    }
  }
  
  /**
  * @expectedException InvalidArgumentException
  */
  public function testSendingNonPlayerInfosToAddUniqueStats() {
    $log = new Log();
    $log->addUpdateUniqueStatsFromPlayerInfos(array("ghg", "asdf"));
  }
}
