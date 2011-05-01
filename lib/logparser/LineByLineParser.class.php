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
  protected $timeout;
  protected $logParser;
  protected $ip;
  protected $port;
  protected $logLineTable;
  protected $keepLooping;
  protected $lastLineRetrievalTmsp;
  protected $lastLineTmsp;
  
  public function __construct($ip, $port) {
    set_time_limit(0); //this process will be going as long as the game, which is more than 30secs.
    $this->delay = sfConfig::get('app_auto_parse_delay_secs');
    $this->sleepSecs = sfConfig::get('app_auto_parse_sleep_secs');
    $this->timeout = sfConfig::get('app_auto_parse_timeout_secs');
    $this->logParser = new LogParser();
    $this->ip = $ip;
    $this->port = $port;
    $this->logLineTable = Doctrine::getTable('LogLine');
    $this->keepLooping = true;
    $this->lastLineRetrievalTmsp = time();
  }
  
  public function start() {
    //sleep for the initial delay time to prevent needless banging on database.
    $this->sleep($this->delay);
    
    do {
      $this->sleep();
      
      $lines = $this->getNewLines();   
      if((!$lines || count($lines) == 0) && time()-$this->timeout >= $this->lastLineRetrievalTmsp) {
        //empty set of lines. if we are past the timeout threshold, put in a game over event, and then quit.
        $lines = array($this->getGameOverLine());
      } else if ($lines && count($lines) > 0) {
        $this->lastLineRetrievalTmsp = time();
        $this->lastLineTmsp = substr($lines[count($lines)-1], 0, 23);
      }
         
      $this->logParser->parseNewLines($lines, $this->ip, $this->port);
      $this->keepLooping = !$this->logParser->isGameOver();
    } while($this->keepLooping);
  }
  
  protected function getGameOverLine() {
    return $this->lastLineTmsp.': Log file closed';
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
  protected function sleep($sleep = null) {
    if(!$sleep) $sleep = $this->sleepSecs;
    do {
      $sleep = sleep($sleep);
    } while ($sleep);
  }
}
