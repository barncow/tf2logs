<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_MiniLogTest extends BaseLogParserTestCase {    
  public function testMiniLog() {
    /**
    NOTE - This log is also testing proper saving of weapons (the garbage weapon key at the end of the log)
    When running this test, make sure that the database is clean to get a true indication of success.
    */
    $log = $this->logParser->parseLogFile($this->LFIXDIR."mini.log", 1);
    $this->assertEquals("09/29/2010 - 19:08:56", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    //$countOfLines-1 represents the lack of chat line with SM command, and the two rcon lines.
    $this->assertEquals($countOfLines-3, count(explode("\n", $log->getLogFile()->getLogData())), "count scrubbed lines == count orig lines subtract line with SM command");
    $this->assertFalse(strpos($log->getLogFile()->getLogData(), "rcon"), "verify that no rcon line is in the log.");
    $this->assertEquals(8, count($log->getStats()), "number of players, should exclude console, specs, and bots");
    
    $this->assertEquals(0, $log->getRedscore(), "red score");
    $this->assertEquals(1, $log->getBluescore(), "blue score");
    
    $this->assertEquals(1666, $log->getElapsedTime(), "elapsed time");
    
    $this->assertEquals(1, $log->getSubmitterPlayerId(), "submitter has correct ID.");
    
    $this->assertEquals("ctf_2fort", $log->getMapName(), "map is ctf_2fort");
    
    $events = $log->getEvents()->toArray();
    $this->assertTrue($events[1]['attacker_player_id'] > 0, "first kill event has attacker 2");
    $this->assertEquals(1, $events[1]['weapon_id'], "first kill event has scattergun");
    $this->assertNotNull($events[1]['assist_player_id'], "first kill event has assist_player_id ");
    $this->assertEquals("-2181 821 -201", $events[1]['assist_coord'], "first kill event has assist_coord ");
    
    $this->assertEquals(1, $events[1]['elapsed_seconds'], "first kill event has elapsed seconds");
    $this->assertEquals("-2419 1637 -511", $events[2]['attacker_coord'], "second kill event has attacker_coord ");
    $this->assertTrue($events[3]['victim_player_id'] > 0, "third kill event has victim ");
    $this->assertEquals('I can also play pyro. I have been doing that a lot on 2fort and doublecross.', $events[4]['text'], "fifth event has text ");
    $this->assertEquals('say_team', $events[4]['event_type'], "fifth event has event_type ");
    $this->assertEquals("-3308 1790 -220", $events[5]['victim_coord'], "sixth event has victim_coord ");
    
    $pce = $events[6]; //pointcapture event
    $this->assertEquals('blue', $pce['team'], "point capture team");
    $this->assertEquals('#Gravelpit_cap_A', $pce['capture_point'], "map capture point");
    $this->assertEquals(3, count($pce['EventPlayers']), "point capture has 3 players");
    $this->assertEquals("C", $pce['EventPlayers'][0]['event_player_type'], "point capture player type is C");
    
    $this->assertEquals(1, $events[8]['blue_score'], "sixth event has blue score 1");
    
    foreach($log->getStats() as $stat) {
      $this->assertNotNull($stat->getTeam(), $stat->getPlayer()->getSteamid()." team is not null");
      if($stat->getPlayer()->getSteamid() == "STEAM_0:0:6845279") {
        //verify numbers for "Target"
        $this->assertEquals("Target", $stat->getPlayer()->getName(), "name on player object should be Target");
        $this->assertEquals("Red", $stat->getTeam(), "Target should be on team Red");
        $this->assertEquals(2, $stat->getKills(), "target's kills");
        $this->assertEquals(1, $stat->getDominations(), "target's dominations");
        $this->assertEquals(1, $stat->getDeaths(), "target's deaths");
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "target's point captures");
        $this->assertEquals(2, $stat->getKillsPerDeath(), "target's kd");
        $this->assertEquals(1, $stat->getFlagCaptures(), "target's flag captures");
        $this->assertEquals(1, $stat->getFlagDefends(), "target's flag defends");
        $this->assertEquals(2, $stat->getLongestKillStreak(), "target's longest kill streak");
        $this->assertEquals('/99/99680cfa3c8e98bba925a92556d8f15fc084df27.jpg', $stat->getPlayer()->getAvatarUrl(), "target has correct avatar url");
        
        foreach($stat->getWeaponStats() as $ws) {
          if($ws->getWeapon()->getKeyName() == "scattergun") {
            $this->assertEquals(2, $ws->getKills(), "target has 2 kills with scatter");
          } else if($ws->getWeapon()->getKeyName() == "sniperrifle") {
            $this->assertEquals(1, $ws->getDeaths(), "target has 1 death to sniperrifle");
          }
        }
        
        foreach($stat->getPlayerStats() as $ps) {
          if($ps->getPlayer()->getSteamid() == "STEAM_0:1:16481274") {
            $this->assertEquals(1, $ps->getKills(), "target has one kill on barncow");
          } else if($ps->getPlayer()->getSteamid() == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps->getKills(), "target has one kill on muffin");
            $this->assertEquals(1, $ps->getDeaths(), "target has one death by muffin");
          }  
        }
        
        foreach($stat->getRoleStats() as $r) {
          if($r->getRole()->getKeyName() == "scout") {
            $this->assertEquals(1666, $r->getTimePlayed(), "target's time as scout");
          } else {
            $this->fail("Target has extra role: ".$r->getRole()->getKeyName());
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:1:16481274") {
        //verify numbers for "Barncow"
        $this->assertEquals(2, $stat->getDeaths(), "barncow's deaths");
        $this->assertEquals(1, $stat->getUbers(), "barncow's ubers");
        $this->assertEquals(1, $stat->getDroppedUbers(), "Barncow dropped uber");
        $this->assertEquals(0.5, $stat->getUbersPerDeath(), "Barncow's uber/d");
        $this->assertEquals(2310, $stat->getHealing(), "Barncow's healing amount");
        
        foreach($stat->getWeaponStats() as $ws) {
          if($ws->getWeapon()->getKeyName() == "scattergun") {
            $this->assertEquals(1, $ws->getDeaths());
          } else if($ws->getWeapon()->getKeyName() == "sniperrifle") {
            $this->assertEquals(1, $ws->getDeaths());
          }
        }
        
        foreach($stat->getPlayerStats() as $ps) {
          if($ps->getPlayer()->getSteamid() == "STEAM_0:0:6845279") {
            $this->assertEquals(1, $ps->getDeaths(), "barncow has one death by target");
          } else if($ps->getPlayer()->getSteamid() == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps->getDeaths(), "barncow has one death by muffin");
          }
        }
        
        foreach($stat->getRoleStats() as $r) {
          if($r->getRole()->getKeyName() == "medic") {
            $this->assertEquals(1666, $r->getTimePlayed(), "barncow's time as medic");
          } else {
            $this->fail("Barncow has extra role: ".$r->getRole()->getKeyName());
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:8581157") {
        //verify numbers for "Cres"
        $this->assertEquals(1, $stat->getAssists(), "cres' assists");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:1:9852193") {
        //verify numbers for "Ctrl+f Muffin!"
        $this->assertEquals(2, $stat->getDeaths(), "Ctrl+f Muffin!'s deaths");
        $this->assertEquals(1, $stat->getTimesDominated(), "Ctrl+f Muffin!'s times dominated");
        $this->assertEquals(1, $stat->getRevenges(), "Ctrl+f Muffin!'s revenges");
        $this->assertEquals(2, $stat->getKills(), "Ctrl+f Muffin!'s kills");
        $this->assertEquals(0, $stat->getDroppedUbers(), "Ctrl+f Muffin! did not drop uber");
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "Ctrl+f Muffin!'s point captures");
        $this->assertEquals(1, $stat->getHeadshots(), "Ctrl+f Muffin!'s headshots");
        
        foreach($stat->getWeaponStats() as $ws) {
          if($ws->getWeapon()->getKeyName() == "tf_projectile_rocket") {

            $this->assertEquals(1, $ws->getDeaths());
          } else if($ws->getWeapon()->getKeyName() == "sniperrifle") {
            $this->assertEquals(1, $ws->getKills());
          } else if($ws->getWeapon()->getKeyName() == "sniperrifle_hs") {
            $this->assertEquals(1, $ws->getKills());
          } else if($ws->getWeapon()->getKeyName() == "scattergun") {
            $this->assertEquals(1, $ws->getDeaths());
          }
        }
        
        foreach($stat->getRoleStats() as $r) {
          if($r->getRole()->getKeyName() == "soldier") {
            $this->assertEquals(211, $r->getTimePlayed(), "muffin's time as soldier");
          } else if($r->getRole()->getKeyName() == "engineer") {
            $this->assertEquals(136, $r->getTimePlayed(), "muffin's time as engineer");
          } else if($r->getRole()->getKeyName() == "sniper") {
            $this->assertEquals(1045, $r->getTimePlayed(), "muffin's time as sniper (cut short due to discon)");
          } else {
            $this->fail("Muffin has extra role: ".$r->getRole()->getKeyName());
          }
        }
        
        foreach($stat->getPlayerStats() as $ps) {
          if($ps->getPlayer()->getSteamid() == "STEAM_0:0:6845279") {
            $this->assertEquals(1, $ps->getDeaths(), "muffin has one death by target");
            $this->assertEquals(1, $ps->getKills(), "muffin killed target once");
          } else if($ps->getPlayer()->getSteamid() == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps->getDeaths(), "muffin has one death by muffin");
          } else if($ps->getPlayer()->getSteamid() == "STEAM_0:1:16481274") {
            $this->assertEquals(1, $ps->getKills(), "muffin killed barncow once");
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:11710749") {
        //verify numbers for "perl"
        $this->assertEquals(1, $stat->getExtinguishes(), "perl's extinguishes");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:556497") {
        //verify numbers for "[H2K]BubbleAlan ʚϊɞ"
        $this->assertEquals(0, $stat->getDroppedUbers(), "Alan did not drop uber");
        
        foreach($stat->getWeaponStats() as $ws) {
          if($ws->getWeapon()->getKeyName() == "world") {
            $this->assertEquals(1, $ws->getDeaths());
          }
        }
        
        foreach($stat->getPlayerStats() as $ps) {
          if($ps->getPlayer()->getSteamid() == "STEAM_0:0:556497") {
            $this->assertEquals(1, $ps->getDeaths(), "alan has one death by alan");
          }
        }
        
        foreach($stat->getRoleStats() as $r) {
          if($r->getRole()->getKeyName() == "medic") {
            $this->assertEquals(1666, $r->getTimePlayed(), "alan's time as medic");
          } else {
            $this->fail("Alan has extra role: ".$r->getRole()->getKeyName());
          }
        }
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:12272740") {
        //verify numbers for "[!?] cheap"
        $this->assertEquals(1, $stat->getCapturePointsCaptured(), "cheap's point captures");
      } else if($stat->getPlayer()->getSteamid() == "STEAM_0:0:973270") {
        //verify numbers for "`yay!"
        $this->assertEquals(1, $stat->getCapturePointsBlocked(), "yay's point blocks");
        $this->assertEquals(1, $stat->getBackstabs(), "yay's backstabs");
        
        foreach($stat->getWeaponStats() as $ws) {
          if($ws->getWeapon()->getKeyName() == "knife_bs") {
            $this->assertEquals(1, $ws->getKills());
            break;
          }
        }
      }
    }
  }
}
