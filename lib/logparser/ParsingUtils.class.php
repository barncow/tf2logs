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
    preg_match("/^".quotemeta($type)."/", $lineDetails, $matches);
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
    preg_match("/^\".+?<\d+?><[A-Za-z0-9:_]+?><\w*?>\" ([\w ,]+)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return trim($matches[1]);
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves the value in quotes immediately following the playerLineAction
  */
  public function getPlayerLineActionDetail($logLineDetails) {
    $matches;
    preg_match("/^\".+?<\d+?><[A-Za-z0-9:_]+?><\w*?>\" [\w ]+? \"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return trim($matches[1]);
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves whether or not the player in the "medic_death" trigger died with uber or not.
  */
  public function didMedicDieWithUber($logLineDetails) {
    $matches;
    preg_match("/\(ubercharge \"(\d)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return (boolean) $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Gets the quoted value after a world trigger
  */
  public function getWorldTriggerAction($logLineDetails) {
    $matches;
    preg_match("/\"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Gets the team for a team line.
  */
  public function getTeamFromTeamLine($logLineDetails) {
    $matches;
    preg_match("/^Team \"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Gets the map for a loading map line.
  */
  public function getMapFromMapLine($logLineDetails) {
    $matches;
    preg_match("/^Loading map \"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[1];
    } else {
      //no matches, so let's try another map line
      preg_match("/^Started map \"(.+?)\"/", $logLineDetails, $matches);
      if(count($matches) > 1) {
        return $matches[1];
      } else {
        return false;
      }
    }
  }
  
  /**
  * Gets the action for a team line.
  */
  public function getTeamAction($logLineDetails) {
    $matches;
    preg_match("/^Team \"(.+?)\" (.+?) \"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[2];
    } else {
      return false;
    }
  }
  

  /**
  * Gets the trigger action for a team line.
  */
  public function getTeamTriggerAction($logLineDetails) {
    $matches;
    preg_match("/^Team \"(.+?)\" triggered \"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[2];
    } else {
      return false;
    }
  }
  
  /**
  * Gets the score for a team line.
  */
  public function getTeamScore($logLineDetails) {
    $matches;
    preg_match("/^Team \"(.+?)\" (current|final) score \"(\d+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[3];
    } else {
      return false;
    }
  }
  
  /**
  * Gets the score for a team line.
  */
  public function getTeamNumberPlayers($logLineDetails) {
    $matches;
    preg_match("/^Team \"(.+?)\" (current|final) score \"(\d+?)\" with \"(\d+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 1) {
      return $matches[4];
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
  
  /**
	* this will get the actual name for the filename given.
	*/
	public function getNameFromFilename($filename) {
	  $matches;
    preg_match("/(.*)(\/)(.+)$/", $filename, $matches);
    if(count($matches) > 0) {
      return $matches[3];
    } else {
      return false;
    }
	}
	
	/**
	* this will get the weapon for the loglinedetails given.
	*/
	public function getWeapon($logLineDetails) {
	  $matches;
    preg_match("/ with \"(.+?)\"/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    }
    
    //still here, try getting weapon from (weapon "scattergun")
    preg_match("/\(weapon \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
	}
	
	/**
	* This retrieves the numeric steam id from the open ID address given from Steam.
	*/
	public function getNumericSteamidFromOpenID($openid) {
	  $matches;
    preg_match("/\/(\d+)$/", $openid, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
	}
	
	/**
  * Retrieves the "attacker" or "victim" coordinates in a kill line.
  */
  public function getKillCoords($type, $logLineDetails) {
    $matches;
    preg_match("/\(".$type."_position \"([\d- ]+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves cpname from a point captured line.
  */
  public function getCapturePointName($logLineDetails) {
    $matches;
    preg_match("/\(cpname \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves the healing value for a medic_death event.
  */
  public function getHealing($logLineDetails) {
    $matches;
    preg_match("/\(healing \"(\d+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return (int) $matches[1];
    }
    //still here, did not get the healing value. Try the supplemental stats version.
    preg_match('/triggered "healed" (\d+?) against/', $logLineDetails, $matches);
    if(count($matches) > 0) {
      return (int) $matches[1];
    } else {
      //still here, did not get the healing value. Try the superlogs version.
      preg_match("/\(heal \"(\d+?)\"\)/", $logLineDetails, $matches);
      if(count($matches) > 0) {
        return (int) $matches[1];
      } else {
        return false;
      }
    }
  }
  
  /**
  * Retrieves the damage value 
  */
  public function getDamage($logLineDetails) {
    $matches;
    //superlog damage
    preg_match("/\(damage \"(\d+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return (int) $matches[1];
    } else {
      //cinq's damage plugin - v1
      preg_match("/ (\d+)$/", $logLineDetails, $matches);
      if(count($matches) > 0) {
        return (int) $matches[1];
      } else {
        return 0; //returning zero instead of false because if no value is found, we really don't care.
      }
    }
  }
  
  /**
  * Retrieves the event value for a flagevent.
  */
  public function getFlagEvent($logLineDetails) {
    $matches;
    preg_match("/\(event \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves team name from round_win event
  */
  public function getRoundWinTeam($logLineDetails) {
    $matches;
    preg_match("/\(winner \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves customkill from kill event
  */
  public function getCustomKill($logLineDetails) {
    $matches;
    preg_match("/\(customkill \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves object from built_object
  */
  public function getObjectFromBuiltObject($logLineDetails) {
    $matches;
    preg_match("/\(object \"(.+?)\"\)/", $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
  
  /**
  * Retrieves item from picked up item line
  */
  public function getPickedUpItemKeyName($logLineDetails) {
    $matches;
    preg_match('/ picked up item "(.+?)"$/', $logLineDetails, $matches);
    if(count($matches) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }
}
?>
