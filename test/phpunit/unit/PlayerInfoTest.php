<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_PlayerInfoTest extends BaseLogParserTestCase {  
  public function testGetPlayerFromString() {
    $this->assertEquals(new PlayerInfo("Console", "Console", "Console"), PlayerInfo::getPlayerFromString('"Console<0><Console><Console>"'), "verify that console string returns player");
    $this->assertEquals(new PlayerInfo("Target", "STEAM_0:0:6845279", null), PlayerInfo::getPlayerFromString('"Target<46><STEAM_0:0:6845279><>"'), "verify that actual player string without team returns player");
    $this->assertEquals(new PlayerInfo("Target", "STEAM_0:0:6845279", null), PlayerInfo::getPlayerFromString('"Target<46><STEAM_0:0:6845279><Unassigned>"'), "verify that actual player string with unassigned team returns player");
    $this->assertEquals(false, PlayerInfo::getPlayerFromString('"Numnutz<17><BOT><Red>"'), "verify that bot returns false");
  }
  
  /**
  * @expectedException InvalidPlayerStringException
  */
  public function testGetInvalidPlayerFromString() {
    PlayerInfo::getPlayerFromString('"Console<0><Conso"');
  }
  
  public function testGetPlayerStringsFromLogLine() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_console_say.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"Console<0><Console><Console>"'), PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "console player string is retrieved from say command");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_enteredgame.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"Target<46><STEAM_0:0:6845279><>"'), PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "actual player string without team is retrieved from entered game entry");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_jointeam.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"Target<46><STEAM_0:0:6845279><Unassigned>"'), PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "actual player string with unassigned team is retrieved from join team entry");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"Target<46><STEAM_0:0:6845279><Blue>"', '"FSTNG! Barncow<48><STEAM_0:1:16481274><Red>"'),
      PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "should grab two players, in order, from kill line");
     
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_triggered_pointcaptured.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"Target<46><STEAM_0:0:6845279><Blue>"', '"Ctrl+f Muffin!<50><STEAM_0:1:9852193><Blue>"', '"[!?] cheap<56><STEAM_0:0:12272740><Blue>"'),
      PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "player string retrieved from point captured entry");
     
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_triggered_medicdeath.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array('"[H2K]BubbleAlan ʚϊɞ<55><STEAM_0:0:556497><Red>"', '"[H2K]BubbleAlan ʚϊɞ<55><STEAM_0:0:556497><Red>"'),
      PlayerInfo::getPlayerStringsFromLogLineDetails($logLineDetails), "actual player string from medic death");
  }
  
  public function testGetAllPlayersFromLogLineDetails() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_console_say.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("Console", "Console", "Console")), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that console say string returns console playerInfo");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_enteredgame.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("Target", "STEAM_0:0:6845279", null)), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that player entered string returns playerInfo");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_jointeam.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("Target", "STEAM_0:0:6845279", null)), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that player join team string returns playerInfo");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_namechange.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("ǤooB", "STEAM_0:1:23384772", null)), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that player with spectator team string returns playerInfo");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("Target", "STEAM_0:0:6845279", "Blue"), new PlayerInfo("FSTNG! Barncow", "STEAM_0:1:16481274", "Red")),
     PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that player kill string returns 2 playerInfos");
     
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_bot_medic.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that bot does not return playerinfo.");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_triggered_pointcaptured_bot.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo('Target', 'STEAM_0:0:6845279', 'Blue')
      , new PlayerInfo('Ctrl+f Muffin!','STEAM_0:1:9852193','Blue')), PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that bot is not in playerinfo array.");
  }
  
  public function testEquals() {
    $pi1 = new PlayerInfo("FSTNG! Barncow", "STEAM_0:1:16481274", "Blue");
    $pi2 = new PlayerInfo("Ctrl+f Muffin!", "STEAM_0:1:9852193", "Red");
    $pi3 = new PlayerInfo("FSTNG! Barncow", "STEAM_0:1:16481274", "Blue");
    
    $this->assertTrue($pi1->equals($pi1), "PI equal to itself");
    $this->assertFalse($pi1->equals($pi2), "PI1 not equal to PI2");
    $this->assertTrue($pi1->equals($pi3), "Diff objs, same player info, are equal");
  }
}
