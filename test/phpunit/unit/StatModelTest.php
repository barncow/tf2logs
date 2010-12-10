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
  
  public function testGetKillsPerDeath() {
    $stat = new Stat();
    
    $stat->setKills(0);
    $stat->setDeaths(0);
    $this->assertEquals(0, $stat->getKillsPerDeath(), "0/0");
    
    $stat->setKills(1);
    $stat->setDeaths(0);
    $this->assertEquals(1, $stat->getKillsPerDeath(), "1/0");
    
    $stat->setKills(3);
    $stat->setDeaths(2);
    $this->assertEquals(1.5, $stat->getKillsPerDeath(), "3/2");
  }
  
  public function testGetUbersPerDeath() {
    $stat = new Stat();
    
    $stat->setUbers(0);
    $stat->setDeaths(0);
    $this->assertEquals(0, $stat->getUbersPerDeath(), "0/0");
    
    $stat->setUbers(1);
    $stat->setDeaths(0);
    $this->assertEquals(1, $stat->getUbersPerDeath(), "1/0");
    
    $stat->setUbers(3);
    $stat->setDeaths(2);
    $this->assertEquals(1.5, $stat->getUbersPerDeath(), "3/2");
  }
  
  public function testInsertWeapon() {
    $log = new Log();
    $stat = new Stat();
    $weapon = new Weapon();
    $weapon->setKeyName('scattergun');
    $stat->Weapons[] = $weapon;
    $log->Stats[] = $stat;
    $log->save();
  }
}
