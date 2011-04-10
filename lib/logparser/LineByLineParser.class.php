<?php
require_once('LogParser.class.php');

/**
 * Handles getting lines from the UDP server and processing them while a game is going on.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class LineByLineParser {
  protected $delay;
  protected $sleepSecs;
  protected $logParser;
  protected $ip;
  protected $port;
  protected $logLineTable;
  protected $keepLooping;
  
  public function __construct($ip, $port) {
    set_time_limit(0); //this process will be going as long as the game, which is more than 30secs.
    $this->delay = sfConfig::get('app_auto_parse_delay_secs');
    $this->sleepSecs = sfConfig::get('app_auto_parse_sleep_secs');
    $this->logParser = new LogParser();
    $this->ip = $ip;
    $this->port = $port;
    $this->logLineTable = Doctrine::getTable('LogLine');
    $this->keepLooping = true;
  }
  
  public function start() {
    do {
      $this->sleep();
      $lines = $this->getNewLines();      
      $this->logParser->parseNewLines($lines, $this->ip, $this->port);
      $this->keepLooping = !$this->logParser->isGameOver();
    } while($this->keepLooping);
  }
  
  protected function getNewLines() {
    return $this->logLineTable->getLogLinesForServer($this->ip, $this->port, $this->delay);
  }
  
  protected function println($s) {
    echo date('H:i:s').' - '.$s."\n";
  }
  
  /**
    Sleep can be interrupted, but it will return num of secs left to sleep, otherwise a falsy value if done or error.
  */
  protected function sleep() {
    $sleep = $this->sleepSecs;
    do {
      $sleep = sleep($sleep);
    } while ($sleep);
  }
}
