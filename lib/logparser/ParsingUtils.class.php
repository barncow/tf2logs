<?php

/**
 * Provides helper methods to parse data from the logs.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class ParsingUtils {

  /**
  * Parses out the timestamp for the log line. Will return a DateTime object
  * of the data if valid; otherwise it will return false.
  * Be sure to use $datetime !== false!
  */
  public function getTimestamp($logLine) {
    if(strlen($logLine) < 24) return false;
    $ts = substr($logLine, 2, 21);
    $dt =  DateTime::createFromFormat("m/d/Y - H:i:s", $ts);
    return $dt;
  }
  
  /**
  * Returns everything past the timestamp of a logLine. Returns false if the
  * line seems corrupted.
  */
  public function getLineDetails($logLine) {
    $matches;
    preg_match("/^L \d\d\/\d\d\/\d\d\d\d \- \d\d:\d\d:\d\d: (.+)$/", $logLine, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Searches the beginning of the lineDetails for a log line for the given type string.
  * To save performance, if a getLineDetails call was already made, you can use the result here.
  */
  public function isLogLineOfType($logLine, $type, $lineDetails = null) {
    if($lineDetails == null) $lineDetails = $this->getLineDetails($logLine);
    $matches;
    preg_match("/^$type/", $lineDetails, $matches);
    return count($matches) > 0;
  }
  
  /**
  * Scrubs the line of any IPs.
  */
  public function scrubLogLine($logLine) {
    return preg_replace("/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/", "255.255.255.255", $logLine);
  }
  
  /**
  * Retrieves the action for a player line.
  * A player line is a line that starts with player information after the timestamp.
  * The action is the text from the start of the first player information to a
  * breaking punctuation, or the end of the string.
  */
  public function getPlayerLineAction($logLineDetails) {
    $matches;
    preg_match("/^\".+?<\d+?><[A-Za-z0-9:_]+?><\w*?>\" ([\w ]+)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return trim($matches[1]);
    } else {
      return false;
    }
  }
  
  public function getPlayerLineActionDetail($logLineDetails) {
    $matches;
    preg_match("/^\".+?<\d+?><[A-Za-z0-9:_]+?><\w*?>\" [\w ]+? \"(.+)\"/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return trim($matches[1]);
    } else {
      return false;
    }
  }
  
  /**
  * For a server_cvar line, gets the value in the first set of quotes, which will be the cvarname.
  */
  public function getServerCvarName($logLineDetails) {
    $matches;
    preg_match("/^server_cvar: \"(.+)\" \".+\"$/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * For a server_cvar line, gets the value in the second set of quotes, which will be the cvar value.
  */
  public function getServerCvarValue($logLineDetails) {
    $matches;
    preg_match("/^server_cvar: \".+\" \"(.+)\"$/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }

}
?>
