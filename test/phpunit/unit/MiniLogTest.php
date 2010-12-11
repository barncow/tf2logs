<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_MiniLogTest extends BaseLogParserTestCase {  
  
  public function testMiniLog() {
    $log = $this->logParser->parseLogFile($this->LFIXDIR."mini.log");
    $this->assertEquals("09/29/2010 - 19:08:56", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    $this->assertEquals($countOfLines, count(explode("\n", $log->getLogFile()->getLogData()))-1, "count scrubbed lines == count orig lines");
    $this->assertEquals(8, count($log->getStats()), "number of players, should exclude console and specs");
    
    $this->assertEquals(0, $log->getRedscore(), "red score");
    $this->assertEquals(1, $log->getBluescore(), "blue score");
    
    //commented until figure out best way to truncate data for each test.
    //$weapons = Doctrine::getTable('Weapon')->createQuery()->execute();
    //$this->assertEquals(count($weapons), 4, "Has correct number of weapons");
    
    foreach($log->getStats() as $stat) {
      $this->assertNotNull($stat->getTeam(), $stat->getPlayer()->getSteamid()." team is not null");
      if($stat->getPlayer()->getSteamid() == "STEAM_0:0:6845279") {
        //verify numbers for "Target"
        $this->assertEquals("Blue", $stat->getTeam(), "Target should be on team Blue");
        $this->assertEquals(2, $stat->getKills(), "target's kills");
        $this->assertEquals(1, $stat->getDominations(), "target's dominations");
        $this->assertEquals(1, $stat->getDeaths(), "target's deaths");
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "target's point captures");
        $this->assertEquals(2, $stat->getKillsPerDeath(), "target's kd");
        
        foreach($stat->getWeapons() as $w) {
          if($w->getKeyName() != "scattergun") {
            $this->fail("Target has extra weapon: ".$w->getKeyName());
          }
        }
        
        foreach($stat->getRoles() as $r) {
          if($r->getKeyName() != "scout") {
            $this->fail("Target has extra role: ".$r->getKeyName());
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:1:16481274") {
        //verify numbers for "Barncow"
        $this->assertEquals(2, $stat->getDeaths(), "barncow's deaths");
        $this->assertEquals(1, $stat->getDestroyedobjects(), "barncow's destroyed objects");
        $this->assertEquals(1, $stat->getUbers(), "barncow's ubers");
        $this->assertEquals(1, $stat->getDroppedUbers(), "Barncow dropped uber");
        $this->assertEquals(0.5, $stat->getUbersPerDeath(), "Barncow's uber/d");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:8581157") {
        //verify numbers for "Cres"
        $this->assertEquals(1, $stat->getAssists(), "cres' assists");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:1:9852193") {
        //verify numbers for "Ctrl+f Muffin!"
        $this->assertEquals(2, $stat->getDeaths(), "Ctrl+f Muffin!'s deaths");
        $this->assertEquals(1, $stat->getTimesDominated(), "Ctrl+f Muffin!'s times dominated");
        $this->assertEquals(1, $stat->getRevenges(), "Ctrl+f Muffin!'s revenges");
        $this->assertEquals(1, $stat->getBuiltobjects(), "Ctrl+f Muffin!'s built objects");
        $this->assertEquals(2, $stat->getKills(), "Ctrl+f Muffin!'s kills");
        $this->assertEquals(0, $stat->getDroppedUbers(), "Ctrl+f Muffin! did not drop uber");
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "Ctrl+f Muffin!'s point captures");
        
        foreach($stat->getWeapons() as $w) {
          if($w->getKeyName() != "tf_projectile_rocket" && $w->getKeyName() != "sniperrifle" ) {
            $this->fail("Muffin has extra weapon: ".$w->getKeyName());
          }
        }
        
        foreach($stat->getRoles() as $r) {
          if($r->getKeyName() != "sniper") {
            $this->fail("Muffin has extra role: ".$r->getKeyName());
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:11710749") {
        //verify numbers for "perl"
        $this->assertEquals(0, $stat->getDestroyedobjects(), "perl's destroyed objects - should be zero since do not want to count own destructions");
        $this->assertEquals(1, $stat->getExtinguishes(), "perl's extinguishes");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:556497") {
        //verify numbers for "[H2K]BubbleAlan ʚϊɞ"
        $this->assertEquals(0, $stat->getDroppedUbers(), "Alan did not drop uber");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:12272740") {
        //verify numbers for "[!?] cheap"
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "cheap's point captures");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:973270") {
        //verify numbers for "`yay!"
        $this->assertEquals(1, $stat->getCapturePointsBlocked(), "yay's point blocks");
      }
    }
  }
}
