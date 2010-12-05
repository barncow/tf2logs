<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_PlayerInfoTest extends BaseLogParserTestCase {  
  public function testGetPlayerFromString() {
    $this->assertEquals(new PlayerInfo("Console", "Console", "Console"), PlayerInfo::getPlayerFromString('"Console<0><Console><Console>"'), "verify that console string returns player");
    $this->assertEquals(new PlayerInfo("Target", "STEAM_0:0:6845279", null), PlayerInfo::getPlayerFromString('"Target<46><STEAM_0:0:6845279><>"'), "verify that actual player string without team returns player");
    $this->assertEquals(new PlayerInfo("Target", "STEAM_0:0:6845279", null), PlayerInfo::getPlayerFromString('"Target<46><STEAM_0:0:6845279><Unassigned>"'), "verify that actual player string with unassigned team returns player");
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
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(array(new PlayerInfo("Target", "STEAM_0:0:6845279", "Blue"), new PlayerInfo("FSTNG! Barncow", "STEAM_0:1:16481274", "Red")),
     PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails), "verify that player kill string returns 2 playerInfos");
  }
}
