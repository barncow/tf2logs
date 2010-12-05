<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_LogModelTest extends sfPHPUnitBaseTestCase
{
  public function testAddUniqueStatForPlayerInfo() {
   $pi1 = new PlayerInfo("myname", "steamid", "team");
   $pi2 = new PlayerInfo("mynewname", "mynewsteamid", "mynewteam");
   
   $log = new Log();
   $log->addUniqueStatFromPlayerInfo($pi1);
   $this->assertEquals(1, count($log->Stats), "insertion of first record should have 1 stat in log");
   
   $log->addUniqueStatFromPlayerInfo($pi1);
   $this->assertEquals(1, count($log->Stats), "insertion of first record, second time should have 1 stat in log");
   
   $log->addUniqueStatFromPlayerInfo($pi2);
   $this->assertEquals(2, count($log->Stats), "insertion of second record should have 2 stats in log");
  }
  
  public function testAddUniqueStatsForPlayerInfos() {
    $pi1 = new PlayerInfo("myname", "steamid", "team");
    $pi2 = new PlayerInfo("mynewname", "mynewsteamid", "mynewteam");

    $log = new Log();
    $log->addUniqueStatFromPlayerInfo($pi1);
    $log->addUniqueStatsFromPlayerInfos(array($pi1, $pi2));
    $this->assertEquals(2, count($log->Stats), "insertion of array with first inserted record should only have 2");
  }
  
  /**
  * @expectedException InvalidArgumentException
  */
  public function testSendingNonPlayerInfosToAddUniqueStats() {
    $log = new Log();
    $log->addUniqueStatsFromPlayerInfos(array("ghg", "asdf"));
  }
}
