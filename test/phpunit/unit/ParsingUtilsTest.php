<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_ParsingUtilsTest extends BaseLogParserTestCase {  
  public function testGetTimestamp() {
    //sunny case
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertEquals("09/29/2010 - 19:05:47", $this->parsingUtils->getTimestamp($l[0])->format("m/d/Y - H:i:s"));
    
    //sunny case
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline2.log");
    $this->assertEquals("11/23/2010 - 16:28:49", $this->parsingUtils->getTimestamp($l[0])->format("m/d/Y - H:i:s"));
    
    //too short, method should return false
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_truncated_duringdate.log");
    $this->assertFalse($this->parsingUtils->getTimestamp($l[0]));
    
    //garbage data, method should return false from DateTime object
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."blah.log");
    $this->assertFalse($this->parsingUtils->getTimestamp($l[0]));
  }
  
  public function testGetLineDetails() {
    //sunny case
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertEquals('Log file started (file "logs/L0929002.log") (game "/home/barncow/74.122.197.144-27015/srcds_l/orangebox/tf") (version "4317")', $this->parsingUtils->getLineDetails($l[0]));
    
    //too short, method should return false
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_truncated_duringdate.log");
    $this->assertFalse($this->parsingUtils->getLineDetails($l[0]));
    
    //garbage data, method should return false
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."blah.log");
    $this->assertFalse($this->parsingUtils->getLineDetails($l[0]));
  }
  
  public function testIsLineOfType() {
    //garbage data
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."blah.log");
    $this->assertFalse($this->parsingUtils->isLogLineOfType($l[0], "blah"));
    
    //using incorrect line type
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertFalse($this->parsingUtils->isLogLineOfType($l[0], "blah"));
    
    //sunny case
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertTrue($this->parsingUtils->isLogLineOfType($l[0], "Log file started"));
  }
  
  public function testScrubLogLine() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertEquals('L 09/29/2010 - 19:05:47: Log file started (file "logs/L0929002.log") (game "/home/barncow/255.255.255.255-27015/srcds_l/orangebox/tf") (version "4317")'
    , $this->parsingUtils->scrubLogLine($l[0]), "verify that initial line with server IP is scrubbed");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_rcon.log");
    $this->assertEquals('L 09/29/2010 - 19:05:47: rcon from "255.255.255.255:50039": command "exec cevo_stopwatch.cfg"', $this->parsingUtils->scrubLogLine($l[0]));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_connected.log");
    $this->assertEquals('L 09/29/2010 - 19:06:32: "Cres<49><STEAM_0:0:8581157><>" connected, address "255.255.255.255:27005"', $this->parsingUtils->scrubLogLine($l[0]));
  }
  
  public function testGetPlayerLineAction() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_console_say.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('say', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_enteredgame.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('entered the game', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('killed', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_disconnected.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('disconnected', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_connected.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('connected, address', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
  }
  
  public function testGetPlayerLineActionDetail() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_jointeam.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('Blue', $this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_triggered_killassist.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('kill assist', $this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_changerole.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('scout', $this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_teamsay.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('I can also play pyro. I have been doing that a lot on 2fort and doublecross.', $this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
  }
  
  public function testDidMedicDieWithUber() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_triggered_medicdeath.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertFalse($this->parsingUtils->didMedicDieWithUber($logLineDetails), "med did not die with uber");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_triggered_medicdeath_withuber.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertTrue($this->parsingUtils->didMedicDieWithUber($logLineDetails), "med died with uber");
  }
  
  public function testGetWorldTriggerAction() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_world_triggered_roundstart.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals("Round_Start", $this->parsingUtils->getWorldTriggerAction($logLineDetails), "got world trigger round_start");
  }
  
  public function testGetTeamFromTeamLine() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_currentscore.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals("Red", $this->parsingUtils->getTeamFromTeamLine($logLineDetails), "got team line team");
  }
  
  public function testGetTeamAction() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_triggered_pointcaptured.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals("triggered", $this->parsingUtils->getTeamAction($logLineDetails), "got team action 'trigger'");
  }
  
  public function testGetTeamTriggerAction() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_triggered_pointcaptured.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals("pointcaptured", $this->parsingUtils->getTeamTriggerAction($logLineDetails), "got team trigger pointcaptured");
  }
  
  public function testGetTeamScore() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_currentscore.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(0, $this->parsingUtils->getTeamScore($logLineDetails), "got team line score");
  }
  
  public function testGetTeamNumberPlayers() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_currentscore_noplayers.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(0, $this->parsingUtils->getTeamNumberPlayers($logLineDetails), "got team line number of players == 0");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_team_currentscore.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals(6, $this->parsingUtils->getTeamNumberPlayers($logLineDetails), "got team line number of players == 6");
  }
  
  public function testProcessServerCvarLine() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_servercvar.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    
    $this->assertEquals('mp_falldamage', $this->parsingUtils->getServerCvarName($logLineDetails), "can get server cvar name");
    $this->assertEquals(0, $this->parsingUtils->getServerCvarValue($logLineDetails), "can get server cvar value");
  }
  
  public function testGetNameFromFilename() {
    $this->assertEquals('full_withGarbageAtEnd.log',
      $this->parsingUtils->getNameFromFilename('/home/barncow/tf2logs/test/fixtures/LogParser/full_withGarbageAtEnd.log'), "can get name from filename with path");
  }
  
  public function testGetWeapon() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('scattergun', $this->parsingUtils->getWeapon($logLineDetails), "can get weapon from player kill line");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_suicide_rocket.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('tf_projectile_rocket', $this->parsingUtils->getWeapon($logLineDetails), "can get weapon from player suicide line");
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_killed_pistolscout.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('pistol_scout', $this->parsingUtils->getWeapon($logLineDetails), "can get pistol_scout");
  }
  
  public function testGetKillCoords() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_kill.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('-704 1584 -464', $this->parsingUtils->getKillCoords("attacker", $logLineDetails), "can get attacker coords");
    $this->assertEquals('-824 1429 -396', $this->parsingUtils->getKillCoords("victim", $logLineDetails), "can get victim coords");
  }
  
  public function testGetNumericSteamidFromOpenID() {
    $this->assertEquals(76561197993228277, $this->parsingUtils->getNumericSteamidFromOpenID("http://steamcommunity.com/openid/id/76561197993228277"));
  }
}
