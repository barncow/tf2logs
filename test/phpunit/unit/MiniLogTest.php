<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_MiniLogTest extends BaseLogParserTestCase {    
  public function testMiniLog() {
    /**
    NOTE - This log is also testing proper saving of weapons (the garbage weapon key at the end of the log)
    When running this test, make sure that the database is clean to get a true indication of success.
    
    Also being tested is disconnected due to no steam login lines - which appear to have a line break which corrupts the log.
    */
    $logid = $this->logParser->parseLogFile($this->LFIXDIR."mini.log", 1);
    $log = Doctrine::getTable('Log')->getLogByIdAsArray($logid);
    $events = Doctrine::getTable('Event')->getEventsByIdAsArray($logid);
    $logfile = Doctrine::getTable('LogFile')->findOneByLogId($logid);
    
    //this assertion will no longer be valid since we are now getting the log after the save. Keeping this around in case this changes.
    //$this->assertEquals("09/29/2010 - 19:08:56", $log->get_timeStart()->format("m/d/Y - H:i:s"), "getTimeStart is correct");
    
    $countOfLines = count($this->logParser->getRawLogFile($this->LFIXDIR."mini.log"));
    //$countOfLines-1 represents the lack of chat line with SM command, and the two rcon lines.
    $this->assertEquals($countOfLines-3, count(explode("\n", $logfile->getLogData())), "count scrubbed lines == count orig lines subtract line with SM command");
    $this->assertFalse(strpos($logfile->getLogData(), "rcon"), "verify that no rcon line is in the log.");
    $this->assertEquals(8, count($log['Stats']), "number of players, should exclude console, specs, and bots");
    
    $this->assertEquals(0, $log['redscore'], "red score");
    $this->assertEquals(1, $log['bluescore'], "blue score");
    
    $this->assertEquals(1666, $log['elapsed_time'], "elapsed time");
    
    $this->assertEquals(1, $log['submitter_player_id'], "submitter has correct ID.");
    
    $this->assertEquals("ctf_2fort", $log['map_name'], "map is ctf_2fort");
    
    //Events
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
    
    //Stats
    foreach($log['Stats'] as $stat) {
      $this->assertNotNull($stat['team'], $stat['Player']['steamid']." team is not null");
      if($stat['Player']['steamid'] == "STEAM_0:0:6845279") {
        //verify numbers for "Target"
        $this->assertEquals("Target", $stat['Player']['name'], "name on player object should be Target");
        $this->assertEquals("Red", $stat['team'], "Target should be on team Red");
        $this->assertEquals(2, $stat['kills'], "target's kills");
        $this->assertEquals(1, $stat['dominations'], "target's dominations");
        $this->assertEquals(1, $stat['deaths'], "target's deaths");
        $this->assertEquals(1, $stat['capture_points_captured'], "target's point captures");
        //$this->assertEquals(2, $stat->getKillsPerDeath(), "target's kd");
        $this->assertEquals(1, $stat['flag_captures'], "target's flag captures");
        $this->assertEquals(1, $stat['flag_defends'], "target's flag defends");
        $this->assertEquals(2, $stat['longest_kill_streak'], "target's longest kill streak");
        $this->assertEquals('/99/99680cfa3c8e98bba925a92556d8f15fc084df27.jpg', $stat['Player']['avatar_url'], "target has correct avatar url");
        
        $wstats = Doctrine::getTable('WeaponStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($wstats) > 0);
        foreach($wstats as $ws) {
          if($ws['Weapon']['key_name'] == "scattergun") {
            $this->assertEquals(2, $ws['kills'], "target has 2 kills with scatter, excluding DR kill");
          } else if($ws['Weapon']['key_name'] == "sniperrifle") {
            $this->assertEquals(1, $ws['deaths'], "target has 1 death to sniperrifle");
          }
        }
        
        $pstats = Doctrine::getTable('PlayerStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($pstats) > 0);
        foreach($pstats as $ps) {
          if($ps['Player']['steamid'] == "STEAM_0:1:16481274") {
            $this->assertEquals(1, $ps['kills'], "target has one kill on barncow");
          } else if($ps['Player']['steamid'] == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps['kills'], "target has one kill on muffin");
            $this->assertEquals(1, $ps['deaths'], "target has one death by muffin");
          }  
        }
        
        $rstats = Doctrine::getTable('RoleStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($rstats) > 0);
        foreach($rstats as $r) {
          if($r['Role']['key_name'] == "scout") {
            $this->assertEquals(1666, $r['time_played'], "target's time as scout");
          } else {
            $this->fail("Target has extra role: ".$r['Role']['key_name']);
          }
        }
      } else if($stat['Player']['steamid'] == "STEAM_0:1:16481274") {
        //verify numbers for "Barncow"
        $this->assertEquals(2, $stat['deaths'], "barncow's deaths");
        $this->assertEquals(1, $stat['ubers'], "barncow's ubers");
        $this->assertEquals(1, $stat['dropped_ubers'], "Barncow dropped uber");
        //$this->assertEquals(0.5, $stat->getUbersPerDeath(), "Barncow's uber/d");
        $this->assertEquals(2310, $stat['healing'], "Barncow's healing amount");
        
        $wstats = Doctrine::getTable('WeaponStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($wstats) > 0);
        foreach($wstats as $ws) {
          if($ws['Weapon']['key_name'] == "scattergun") {
            $this->assertEquals(1, $ws['deaths']);
          } else if($ws['Weapon']['key_name'] == "sniperrifle") {
            $this->assertEquals(1, $ws['deaths']);
          }
        }
        
        $pstats = Doctrine::getTable('PlayerStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($pstats) > 0);
        foreach($pstats as $ps) {
          if($ps['Player']['steamid'] == "STEAM_0:0:6845279") {
            $this->assertEquals(1, $ps['deaths'], "barncow has one death by target");
          } else if($ps['Player']['steamid'] == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps['deaths'], "barncow has one death by muffin");
          }
        }
        
        $phstats = Doctrine::getTable('PlayerHealStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($phstats) > 0);
        foreach($phstats as $ps) {
          if($ps['Player']['steamid'] == "STEAM_0:0:8581157") {
            $this->assertEquals(72, $ps['healing'], "barncow has healing for cres");
          } else if($ps['Player']['steamid'] == "STEAM_0:0:6845279") {
            $this->assertEquals(27, $ps['healing'], "barncow has healing for target");
          }
        }
        
        $rstats = Doctrine::getTable('RoleStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($rstats) > 0);
        foreach($rstats as $r) {
          if($r['Role']['key_name'] == "medic") {
            $this->assertEquals(1666, $r['time_played'], "barncow's time as medic");
          } else {
            $this->fail("Barncow has extra role: ".$r['Role']['key_name']);
          }
        }
      } else if($stat['Player']['steamid'] == "STEAM_0:0:8581157") {
        //verify numbers for "Cres"
        $this->assertEquals(1, $stat['assists'], "cres' assists, excluding DR");
        $this->assertEquals(33, $stat['damage'], "cres' damage");
        
        $ipstats = Doctrine::getTable('ItemPickupStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($ipstats) > 0);
        foreach($ipstats as $ips) {
          if($ips['item_key_name'] == "medkit_small") {
            $this->assertEquals(2, $ips['times_picked_up'], "cres picked up 2 small hps");
          } else if($ips['item_key_name'] == "medkit_medium") {
            $this->assertEquals(1, $ips['times_picked_up'], "cres picked up 1 medium hps");
          }
        }
      } else if($stat['Player']['steamid'] == "STEAM_0:1:9852193") {
        //verify numbers for "Ctrl+f Muffin!"
        $this->assertEquals(2, $stat['deaths'], "Ctrl+f Muffin!'s deaths");
        $this->assertEquals(1, $stat['times_dominated'], "Ctrl+f Muffin!'s times dominated");
        $this->assertEquals(1, $stat['revenges'], "Ctrl+f Muffin!'s revenges");
        $this->assertEquals(2, $stat['kills'], "Ctrl+f Muffin!'s kills");
        $this->assertEquals(0, $stat['dropped_ubers'], "Ctrl+f Muffin! did not drop uber");
        $this->assertEquals(1, $stat['capture_points_captured'], "Ctrl+f Muffin!'s point captures");
        $this->assertEquals(1, $stat['headshots'], "Ctrl+f Muffin!'s headshots");
        
        $wstats = Doctrine::getTable('WeaponStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($wstats) > 0);
        foreach($wstats as $ws) {
          if($ws['Weapon']['key_name'] == "tf_projectile_rocket") {

            $this->assertEquals(1, $ws['deaths']);
          } else if($ws['Weapon']['key_name'] == "sniperrifle") {
            $this->assertEquals(1, $ws['kills']);
          } else if($ws['Weapon']['key_name'] == "sniperrifle_hs") {
            $this->assertEquals(1, $ws['kills']);
          } else if($ws['Weapon']['key_name'] == "scattergun") {
            $this->assertEquals(1, $ws['deaths']);
          }
        }
        
        $rstats = Doctrine::getTable('RoleStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($rstats) > 0);
        foreach($rstats as $r) {
          if($r['Role']['key_name'] == "soldier") {
            $this->assertEquals(211, $r['time_played'], "muffin's time as soldier");
          } else if($r['Role']['key_name'] == "engineer") {
            $this->assertEquals(136, $r['time_played'], "muffin's time as engineer");
          } else if($r['Role']['key_name'] == "sniper") {
            $this->assertEquals(1045, $r['time_played'], "muffin's time as sniper (cut short due to discon)");
          } else {
            $this->fail("Muffin has extra role: ".$r['Role']['key_name']);
          }
        }
        
        $pstats = Doctrine::getTable('PlayerStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($pstats) > 0);
        foreach($pstats as $ps) {
          if($ps['Player']['steamid'] == "STEAM_0:0:6845279") {
            $this->assertEquals(1, $ps['deaths'], "muffin has one death by target");
            $this->assertEquals(1, $ps['kills'], "muffin killed target once");
          } else if($ps['Player']['steamid'] == "STEAM_0:1:9852193") {
            $this->assertEquals(1, $ps['deaths'], "muffin has one death by muffin");
          } else if($ps['Player']['steamid'] == "STEAM_0:1:16481274") {
            $this->assertEquals(1, $ps['kills'], "muffin killed barncow once");
          }
        }
      } else if($stat['Player']['steamid'] == "STEAM_0:0:11710749") {
        //verify numbers for "perl"
        $this->assertEquals(1, $stat['extinguishes'], "perl's extinguishes");
      } else if($stat['Player']['steamid'] == "STEAM_0:0:556497") {
        //verify numbers for "[H2K]BubbleAlan ʚϊɞ"
        $this->assertEquals(0, $stat['dropped_ubers'], "Alan did not drop uber");
        
        $wstats = Doctrine::getTable('WeaponStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($wstats) > 0);
        foreach($wstats as $ws) {
          if($ws['Weapon']['key_name'] == "world") {
            $this->assertEquals(1, $ws['deaths']);
          }
        }
        
        $pstats = Doctrine::getTable('PlayerStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($pstats) > 0);
        foreach($pstats as $ps) {
          if($ps['Player']['steamid'] == "STEAM_0:0:556497") {
            $this->assertEquals(1, $ps['deaths'], "alan has one death by alan");
          }
        }
        
        $rstats = Doctrine::getTable('RoleStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($rstats) > 0);
        foreach($rstats as $r) {
          if($r['Role']['key_name'] == "medic") {
            $this->assertEquals(1666, $r['time_played'], "alan's time as medic");
          } else {
            $this->fail("Alan has extra role: ".$r['Role']['key_name']);
          }
        }
      } else if($stat['Player']['steamid'] == "STEAM_0:0:12272740") {
        //verify numbers for "[!?] cheap"
        $this->assertEquals(1, $stat['capture_points_captured'], "cheap's point captures");
      } else if($stat['Player']['steamid'] == "STEAM_0:0:973270") {
        //verify numbers for "`yay!"
        $this->assertEquals(1, $stat['capture_points_blocked'], "yay's point blocks");
        $this->assertEquals(1, $stat['backstabs'], "yay's backstabs");
        $this->assertEquals(0, $stat['deaths'], "yay's deaths - exclude feign death");
        
        $wstats = Doctrine::getTable('WeaponStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($wstats) > 0);
        foreach($wstats as $ws) {
          if($ws['Weapon']['key_name'] == "knife_bs") {
            $this->assertEquals(1, $ws['kills']);
            break;
          }
        }
        
        $rstats = Doctrine::getTable('RoleStat')->findArrayByStatId($stat['id']);
        $this->assertTrue(count($rstats) > 0);
        foreach($rstats as $r) {
          if($r['Role']['key_name'] == "engineer") {
            $this->fail("yay has engineer role when only spy");
          }
        }
      }
    }
  }
}
