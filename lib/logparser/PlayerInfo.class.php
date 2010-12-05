<?php
/**
 * Represents information from the logs about a player.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class PlayerInfo {
  protected $name;
  protected $steamid;
  protected $team;
  
  function __construct($name, $steamid, $team) {
    $this->setName($name);
    $this->setSteamid($steamid);
    $this->setTeam($team);
  }
  
  /**
  * Convenience method to determine if this playerInfo is equal to
  * the given playerInfo.
  */
  public function equals(PlayerInfo $playerInfo) {
    return $this->getSteamid() == $playerInfo->getSteamid();
  }
  
  /**
  * This will take a logLineDetails string and parse out all of the players in it.
  */
  public static function getAllPlayersFromLogLineDetails($logLineDetails) {
    $a = self::getPlayerStringsFromLogLineDetails($logLineDetails);
    $ret = array();
    foreach($a as $p) {
      $ret[] = self::getPlayerFromString($p);
    }
    return $ret;
  }
  
  /**
  * Will go through a log line and get all player strings.
  */
  public static function getPlayerStringsFromLogLineDetails($logLineDetails) {
    $matches;
    preg_match_all("/(\".+?<\d+?><[A-Za-z0-9:_]+?><\w*?>\")/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Will return a new PlayerInfo instance representing the string given.
  */
  public static function getPlayerFromString($playerString) {
    $matches;
    preg_match("/\"(.+)<\d+><([A-Za-z0-9:_]+)><(\w*)>\"/", $playerString, $matches);
    if(count($matches) > 0) {
      return new PlayerInfo($matches[1], $matches[2], $matches[3]);
    } else {
      throw new InvalidPlayerStringException($playerString);
    }
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function setName($name) {
    $this->name = $name;
  }
  
  public function getSteamid() {
    return $this->steamid;
  }
  
  public function setSteamid($steamid) {
    $this->steamid = $steamid;
  }
  
  public function getTeam() {
    return $this->team;
  }
  
  public function setTeam($team) {
    if($team == "Unassigned" || $team == "Spectator") {
      $team = null;
    }
    $this->team = $team;
  }
}
?>
