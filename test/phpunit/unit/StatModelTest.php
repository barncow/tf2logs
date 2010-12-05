<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_StatModelTest extends sfPHPUnitBaseTestCase {
  public function testEqualToPlayerInfo() {
    $stat = new Stat();
    $stat->setPlayerInfoAttributes(new PlayerInfo("myname", "steamid", "team"));
    $this->assertTrue($stat->equalsPlayerInfo(new PlayerInfo("myname", "steamid", "team")));
  }
}
