<?php

/**
 * Player
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Brian Barnekow
 */
class Player extends BasePlayer {

  public function setSteamid($steamid) {
    $this->_set('steamid', $steamid);
    $this->_set('numeric_steamid', $this->convertStandardToNumericSteamid($steamid));
  }
  
  public function setNumericSteamid($steamid) {
    $this->_set('numeric_steamid', $steamid);
    $this->_set('steamid', $this->convertNumericToStandardSteamid($steamid));
  }
  
  /**
  * Converts textual steamids (ie. STEAM_0:1:20348) to the numeric version (ie. 76561197993228277)
  * Code provided by: http://forums.alliedmods.net/showpost.php?p=565979&postcount=16
  * Given argument should be a string.
  */
  public function convertStandardToNumericSteamid($pszAuthID) {
    $iServer = "0";
    $iAuthID = "0";

    $szAuthID = $pszAuthID;

    $szTmp = strtok($szAuthID, ":");

    while(($szTmp = strtok(":")) !== false) {
      $szTmp2 = strtok(":");
      if($szTmp2 !== false) {
          $iServer = $szTmp;
          $iAuthID = $szTmp2;
      }
    }
    if($iAuthID == "0") return "0";

    $i64friendID = bcmul($iAuthID, "2");

    //Friend ID's with even numbers are the 0 auth server.
    //Friend ID's with odd numbers are the 1 auth server.
    $i64friendID = bcadd($i64friendID, bcadd("76561197960265728", $iServer)); 

    return $i64friendID;
  }
  
  /**
  * Converts numeric steamids (ie. 76561197993228277) to the textual version (ie. STEAM_0:1:20348)
  * Code provided by: http://forums.alliedmods.net/showpost.php?p=565979&postcount=118
  * Given argument should be a string.
  */
  public function convertNumericToStandardSteamid($data) {
    if (substr($data,-1)%2==0) $server=0; else $server=1;
		$auth=bcsub($data,'76561197960265728');
		if (bccomp($auth,'0')!=1) return false;
		$auth=bcsub($auth,$server);
		$auth=bcdiv($auth,2);
		return 'STEAM_0:'.$server.':'.$auth;
  }
}
