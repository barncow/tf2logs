<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_StatModelTest extends sfPHPUnitBaseTestCase {
  public function testEqualToPlayerInfo() {
    $stat = new Stat();
    $stat->setPlayerInfoAttributes(new PlayerInfo("myname", "steamid", "team"));
    $this->assertTrue($stat->equalsPlayerInfo(new PlayerInfo("myname", "steamid", "team")));
  }
  
  public function testIncrementStat() {
    $stat = new Stat();
    $stat->incrementStat("kills");
    $this->assertEquals(1, $stat->getKills(), "first inc");
    $stat->incrementStat("kills");
    $this->assertEquals(2, $stat->getKills(), "second inc");
  }
  
  /**
  * @expectedException InvalidArgumentException
  */
  public function testIncrementStatInvalid() {
    $stat = new Stat();
    $stat->incrementStat("id");
  }
  
  /**
  * @expectedException InvalidArgumentException
  */
  public function testIncrementStatNotFound() {
    $stat = new Stat();
    $stat->incrementStat("lhf;alsdjiusfj");
  }
}
