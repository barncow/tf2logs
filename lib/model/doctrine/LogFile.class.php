<?php

/**
 * LogFile
 * 
 * This will hold the scrubbed log file in the database.
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class LogFile extends BaseLogFile {
  public function appendString($string) {
    if($this->log_data == null) $this->log_data = "";
    if($this->log_data != "") $this->log_data .= "\n";
    $this->log_data .= $string;
  }
}
