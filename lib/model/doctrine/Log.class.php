<?php

/**
 * Log
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Log extends BaseLog
{
  protected $_timeStart;
  protected $_scrubbedLog = "";
  
  public function set_timeStart($timeStart) {
    $this->_timeStart = $timeStart;
  }
  
  public function get_timeStart() {
    return $this->_timeStart;
  }
  
  public function appendToScrubbedLog($logLine) {
    $this->_scrubbedLog .= $logLine."\n";
  }
  
  public function getScrubbedLog() {
    return $this->_scrubbedLog;
  }
}
