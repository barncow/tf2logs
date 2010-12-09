<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once 'BaseLogParserTestCase.php';

class unit_LogParserTest extends BaseLogParserTestCase {
  /**
  * @expectedException LogFileNotFoundException
  */
  public function testGetNotExistingLogFile() {	
    $this->logParser->getRawLogFile($this->LFIXDIR."asodijfsfj.log");
  }
  
  public function testGetExistingLogFile() {	
    $this->assertNotEmpty($this->logParser->getRawLogFile($this->LFIXDIR."blah.log"));
  }
  
  /**
  * @expectedException TournamentModeNotFoundException
  */
  //may want to deal with this as corrupt log file exception
  public function testCorruptFileThrowsCorruptLogLineException() {
    $this->logParser->parseLogFile($this->LFIXDIR."blah.log");
  }
  
  /**
  * @expectedException TournamentModeNotFoundException
  */
  public function testTournamentModeNotFoundException() {
    $this->logParser->parseLogFile($this->LFIXDIR."mini_without_tourney.log");
  }
  
  //the last line of the log file is usually truncated, and corrupt. Ignore exceptions for that line.
  public function testIgnoreUnrecognizedExceptionOnLastLine() {
    $this->logParser->parseLogFile($this->LFIXDIR."mini_truncated.log");
  }
  
  //the last line of the log file is usually truncated, and corrupt. Ignore exceptions for that line.
  public function testIgnoreCorruptExceptionOnLastLine() {
    $this->logParser->parseLogFile($this->LFIXDIR."mini_truncated_corrupt.log");
  }
  
}
