<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_ParsingUtilsTest extends BaseLogParserTestCase {  
  public function testGetTimestamp() {
    //sunny case
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_initialline.log");
    $this->assertEquals("09/29/2010 - 19:05:47", $this->parsingUtils->getTimestamp($l[0])->format("m/d/Y - H:i:s"));
    
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
  }
  
  public function testGetPlayerLineAction() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_console_say.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('say', $this->parsingUtils->getPlayerLineAction($logLineDetails));
    
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_player_enteredgame.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    $this->assertEquals('entered the game', $this->parsingUtils->getPlayerLineAction($logLineDetails));
  }
  
  public function testProcessServerCvarLine() {
    $l = $this->logParser->getRawLogFile($this->LFIXDIR."line_servercvar.log");
    $logLineDetails = $this->parsingUtils->getLineDetails($l[0]);
    
    $this->assertEquals('mp_falldamage', $this->parsingUtils->getServerCvarName($logLineDetails), "can get server cvar name");
    $this->assertEquals(0, $this->parsingUtils->getServerCvarValue($logLineDetails), "can get server cvar value");
  }
}
