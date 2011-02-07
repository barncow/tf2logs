<?php
require_once('exceptions/UnrecognizedLogLineException.class.php');
require_once('exceptions/LogFileNotFoundException.class.php');
require_once('exceptions/CorruptLogLineException.class.php');
require_once('exceptions/InvalidPlayerStringException.class.php');
require_once('exceptions/TournamentModeNotFoundException.class.php');
require_once('ParsingUtils.class.php');
require_once('PlayerInfo.class.php');

/**
 * Handles processing a log and saving the results to a database.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class LogParser {
  protected $parsingUtils;
  protected $log;
  protected $isTournamentMode;
  protected $weapons;
  protected $roles;
  protected $currentDt;
  protected $isCtf;
  protected $playerChangeTeams;
  protected $addToScrubbedLog;
  protected $ignoreUnrecognizedLogLines;
  
  //GAME STATE CONSTANTS
  const GAME_APPEARS_OVER = 0;
  const GAME_OVER = 1;
  const GAME_CONTINUE = 2;
  
  function __construct() {
    $this->setParsingUtils(new ParsingUtils());
    $this->log = new Log();
    $this->isTournamentMode = false; //assume its not, until first world trigger round_start
    $this->buildWeaponCache();
    $this->buildRoleCache();
    $this->isCtf = false;
    $this->addToScrubbedLog = true;
    $this->ignoreUnrecognizedLogLines = sfConfig::get('app_ignore_unrecognized_log_lines');
    
    //will use assoc. array of steamids to determine unique team switches, instead of one player switching multi times.
    $this->playerChangeTeams = array(); 
  }
  
  public function buildWeaponCache() {
    $this->weapons = Doctrine::getTable('Weapon')->getAllWeapons();
  }
  
  public function buildRoleCache() {
    $this->roles = Doctrine::getTable('Role')->findAll();
  }
  
  /**
  * This will find a weapon from the weapon cache. If not found, one will be created
  * and saved to the database, will be added to the cache, and returned.
  */
  public function getWeaponFromCache($keyName) {
    if($keyName === false) return false;
    foreach($this->weapons as $w) {
      if($w->getKeyName() == $keyName) {
        return $w;
      }
    }
    
    //still here, need to create new weapon
    $wep = new Weapon();
    $wep->setKeyName($keyName);
    //setting the weapon name to keyname for easier sorting onscreen.
    $wep->setName($keyName);
    $wep->save();
    $this->weapons[] = $wep;
    return $wep;
  }
  
  /**
  * This will find a role from the role cache. This will not change, so it will not add.
  */
  public function getRoleFromCache($keyName) {
    if($keyName === false) return false;
    foreach($this->roles as $r) {
      if($r->getKeyName() == $keyName) {
        return $r;
      }
    }
    
    //still here, did not find.
    throw new InvalidArgumentException("Invalid role given to getRoleFromCache method: ".$keyName);
  }
  
  public function getLog() {
    return $this->log;
  }
  
  public function setParsingUtils($parsingUtils) {
    $this->parsingUtils = $parsingUtils;
  }
  
  public function getParsingUtils() {
    return $this->parsingUtils;
  }
  
  /**
  * Returns the elapsed time in milliseconds since the beginning of the match.
  */
  public function getElapsedTime($now) {
    if($this->log->get_timeStart() == null) return 0;
    return $now->getTimestamp()-$this->log->get_timeStart()->getTimestamp();
  }
  
  /**
  * Will get a raw log file from the filesystem, as an array.
  */
	public function getRawLogFile($filename) {
	  if(file_exists($filename)) {
			return file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		} else throw new LogFileNotFoundException("Could not find: ".$filename);
	}
	
	/**
	* This will parse the entire log file.
	*/
	public function parseLogFile($filename, $logSubmitterId, $logName = null, $logMapName = null, Log $logObj = null) {
	  if($logName == null) {
	    $logName = $this->parsingUtils->getNameFromFilename($filename);
	  }
	  $this->log->setName($logName);
	  $this->log->setMapName($logMapName);
	  $this->log->setSubmitterPlayerId($logSubmitterId);
	  if($logObj != null) {
	    $this->log = $logObj;
	  }
	  $file = $this->getRawLogFile($filename);
	  return $this->parseLog($file);
	}
	
	/**
	* This will parse the entire log file saved from the database.
	*/
	public function parseLogFromDB($log) {  
	  $file = explode("\n", Doctrine::getTable('LogFile')->findOneByLogId($log->getId())->getLogData());
	  $log->clearLog();
	  $this->log = $log;
	  return $this->parseLog($file);
	}
	
	protected function parseLog($arrayLogLines) {
	  $game_state = null;
	  $fileLength = count($arrayLogLines);
	  
	  foreach($arrayLogLines as $key => $logLine) {
	    try {
	      $game_state = $this->parseLine($logLine);
	    } catch(UnrecognizedLogLineException $ulle) {
	      if($key != $fileLength-1) {
	        //if exception was not thrown on last line, keep throwing it
	        //last lines are generally corrupted, can just pitch them
	        throw $ulle;
	      }
	    } catch(CorruptLogLineException $clle) {
	      if($key != $fileLength-1) {
	        //if exception was not thrown on last line, keep throwing it
	        //last lines are generally corrupted, can just pitch them
	        throw $clle;
	      }
	    }
	    
	    if($game_state == self::GAME_APPEARS_OVER || $game_state == self::GAME_OVER) {
	      break; //do not keep going
	    }
	  }
	  if(!$this->isTournamentMode) {
	    throw new TournamentModeNotFoundException();
	  }
    $this->finishLog();
	  $this->log->save();
	  return $this->log;
	}
	
	/**
	* This will provide a method of processing the line,
	* and being able to do some init and cleanup tasks if needed.
	* @see protected function doLine($logLine)
	*/
	public function parseLine($logLine) {
	  $exception = null;
	  $game_state = null;
	  try {
	    $game_state = $this->doLine($logLine);
	  } catch(Exception $e) {
	    $exception = $e;
	  }
	  $this->afterParseLine($logLine);
	  if($exception != null) throw $exception;
	  return $game_state;
	}
	
	/**
	* Called after the log appears to be finished, to do any last cleanup tasks,
	* and assign any awards. No need to save in this method.
	*/
	protected function finishLog() {
	  $this->log->finishLog($this->currentDt, $this->log->get_timeStart());
	}
	
	/**
	* Do any final tasks for the line, such as scrubbing and adding the log line
	* for the saved log version.
	*/
	protected function afterParseLine($logLine) {
	  if($this->addToScrubbedLog) {
	    $this->log->appendToScrubbedLog($this->parsingUtils->scrubLogLine($logLine));
	  } else {
	    $this->addToScrubbedLog = true;
	  }
	}
	
	/**
	* This will do the processing of the line. This will not be called outside this
	* class. Use parseLine, since that will do some init and cleanup as needed.
	* @see public function parseLine($logLine)
	*/
	protected function doLine($logLine) {
	  //doing a ltrim here to remove any extraneous characters that may corrupt the log.
	  $logLine = substr($logLine, strpos($logLine, "L"));
	  
	  $dt = $this->parsingUtils->getTimestamp($logLine);
	  if($dt === false) {
	    throw new CorruptLogLineException($logLine);
	  }
	  $this->currentDt = $dt;
	  
	  $logLineDetails = $this->parsingUtils->getLineDetails($logLine);
	  
	  //always set elapsed time. When the log finishes, the last timestamp set will be our elapsed time.
	  $elapsedTime = $this->getElapsedTime($dt);
	  $this->log->setElapsedTime($elapsedTime);
	  
	  //check for world trigger, specifically the first round_start of the log. This indicates
	  //that the tournament mode has started, and pregame has ended.
	  if($this->parsingUtils->isLogLineOfType($logLine, "World triggered", $logLineDetails)) {
	    $worldTriggerAction = $this->parsingUtils->getWorldTriggerAction($logLineDetails);
	    if($worldTriggerAction == "Round_Start") {
	      $this->isTournamentMode = true;
	      if($this->log->get_timeStart() == null) {
	        //there will likely be multiple round_start's, only need the first one for the tmsp
	        $this->log->set_timeStart($dt);
	        
	        //adding a round start event for the first round.
	        $this->log->addRoundStartEvent($elapsedTime, 0, 0);
	      }
	      
	      $this->playerChangeTeams = array(); //resetting change teams. assuming players are at where they need to be.
	      
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Game_Over") {
	      return self::GAME_OVER;
	    } else if($worldTriggerAction == "Round_Win") {
	      //increment score for the team that won the round, if the map is not ctf (ctf has own scoring)
	      if(!$this->isCtf) {
	        $team = $this->parsingUtils->getRoundWinTeam($logLineDetails);
	        $this->log->incrementScoreForTeam($team);
	      }
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Round_Setup_Begin"
	      || $worldTriggerAction == "Round_Setup_End"
	      || $worldTriggerAction == "Round_Overtime"
	      || $worldTriggerAction == "Round_Length"
	      || $worldTriggerAction == "Mini_Round_Selected" //not sure what mini rounds are (discovered in 1911 log)
	      || $worldTriggerAction == "Mini_Round_Start"
	      || $worldTriggerAction == "Mini_Round_Length"
	      || $worldTriggerAction == "Mini_Round_Win"
	      ) {
	      return self::GAME_CONTINUE; //no need to process
	    }
	  }	  
	  
	  if(!$this->isTournamentMode) {
	    return self::GAME_CONTINUE; //do not want to track information when not in tournament mode.
	  }
	  
	  //go through line types. When complete with the line, return.
	  if($this->parsingUtils->isLogLineOfType($logLine, "Log file started", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "server_cvar: ", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "Log file closed", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "Your server will be restarted on map change.", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "[META]", $logLineDetails)) {
	    return self::GAME_CONTINUE; //do nothing, just add to scrubbed log
	  } else if($this->parsingUtils->isLogLineOfType($logLine, "rcon from", $logLineDetails)) {
	    //this could contain sensitive information. Do not add it to the log.
	    $this->addToScrubbedLog = false;
	    return self::GAME_CONTINUE;
	  } else if($this->parsingUtils->isLogLineOfType($logLine, '"', $logLineDetails)) {
	    //this will be a player action line. The quote matches the quote on a player string in the log.
	    //Need to determine what action is being done here.
	    $playerLineAction = $this->parsingUtils->getPlayerLineAction($logLineDetails);
	    $players = PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails);
	    $this->log->addUpdateUniqueStatsFromPlayerInfos($players);
	    
	    if($playerLineAction == "entered the game"
	    || $playerLineAction == "connected, address"
	    || $playerLineAction == "STEAM USERID validated"
	    ) {
	      return self::GAME_CONTINUE; //do nothing, just add to scrubbed log
	    } else if($playerLineAction == "say" || $playerLineAction == "say_team") {
	      $txt = $this->parsingUtils->getPlayerLineActionDetail($logLineDetails);
	      
	      if(strpos($txt, "!") === 0 || strpos($txt, "/") === 0) {
	        //lines beginning with ! or / generally are Sourcemod commands, which could hold sensitive info.
	        //do not include in scrubbed log, nor in events.
	        $this->addToScrubbedLog = false;
	        return self::GAME_CONTINUE;
	      }
	      
	      if($players[0]->getSteamid() != "Console") {
	        $p = $players[0];
	        $this->log->addChatEvent($elapsedTime, $playerLineAction, $p, $txt);
	      }
	      
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "disconnected") {
	      $p = $players[0];
	      $this->log->finishStatForSteamid($p->getSteamid(), $dt, $this->log->get_timeStart());
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "changed role to") {
	      $p = $players[0];
	      $this->log->addRoleToSteamid($p->getSteamid(), $this->getRoleFromCache($this->parsingUtils->getPlayerLineActionDetail($logLineDetails)), $dt, $this->log->get_timeStart());
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "joined team") {
	      $p = $players[0];
	      $p->setTeam($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatFromPlayerInfo($p);
	      $this->playerChangeTeams[$p->getSteamid()] = 1;
	      if(count($this->playerChangeTeams) >= count($this->log->getStats())) {
	        //teams have switched, switch scores
	        $this->log->switchScores();
	        $this->log->addScoreChangeEvent($elapsedTime, $this->log->getBluescore(), $this->log->getRedscore());
	        $this->playerChangeTeams = array();
	      }
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "changed name to") {
	      $p = $players[0];
	      $p->setName($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatFromPlayerInfo($p);
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "killed") {
	      $attacker = $players[0];
	      $victim = $players[1];
	      $w = $this->parsingUtils->getWeapon($logLineDetails);
	      $ck = $this->parsingUtils->getCustomKill($logLineDetails);
	      
	      if(($w == "sniperrifle" || $w == "tf_projectile_arrow" || $w == "ambassador") && $ck == "headshot") {
	        //if a headshot occurred, edit the weapon to be the normal weapon's headshot variant.
	        //also, add headshot score
	        $w .= "_hs";
	        $this->log->incrementStatFromSteamid($attacker->getSteamid(), "headshots");
	      }
	      $weapon = $this->getWeaponFromCache($w);
	      
	      $this->log->incrementStatFromSteamid($attacker->getSteamid(), "kills");
	      $this->log->incrementWeaponForPlayer($attacker->getSteamid(), $weapon, 'kills');
	      if($weapon->getRole() != null && $weapon->getRole()->getId() != null) {
	        $this->log->addRoleToSteamid($attacker->getSteamid(), $weapon->getRole(), $dt, $this->log->get_timeStart());
	      }
	      $this->log->addPlayerStatToSteamid($attacker->getSteamid(), $victim->getSteamid(), "kills");
	      
	      $this->log->incrementStatFromSteamid($victim->getSteamid(), "deaths"); 
	      $this->log->incrementWeaponForPlayer($victim->getSteamid(), $weapon, 'deaths');
	      $this->log->addPlayerStatToSteamid($victim->getSteamid(), $attacker->getSteamid(), "deaths");
	      
	      $this->log->addKillEvent($elapsedTime, $weapon->getId(), $attacker->getSteamid(), $this->parsingUtils->getKillCoords("attacker", $logLineDetails)
	        ,$victim->getSteamid(), $this->parsingUtils->getKillCoords("victim", $logLineDetails));
	      
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "committed suicide with") {
	      $p = $players[0];
	      $weapon = $this->getWeaponFromCache($this->parsingUtils->getWeapon($logLineDetails));
	      $this->log->incrementStatFromSteamid($p->getSteamid(), "deaths"); 
	      $this->log->incrementWeaponForPlayer($p->getSteamid(), $weapon, 'deaths');
	      $this->log->addPlayerStatToSteamid($p->getSteamid(), $p->getSteamid(), "deaths");
	      if($weapon->getRole() != null) $this->log->addRoleToSteamid($p->getSteamid(), $weapon->getRole(), $dt, $this->log->get_timeStart());
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "triggered") {	      
	      $playerLineActionDetail = $this->parsingUtils->getPlayerLineActionDetail($logLineDetails);
	      
	      if($playerLineActionDetail == "kill assist") {
	        //this line is a complement to a previous line. Do not increment the victim's death; it was done above.
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "assists"); 
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "domination") {
	        $attacker = $players[0];
	        $victim = $players[1];
	        $this->log->incrementStatFromSteamid($attacker->getSteamid(), "dominations"); 
	        $this->log->incrementStatFromSteamid($victim->getSteamid(), "times_dominated"); 
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "builtobject") {
	        $p = $players[0];
	        $this->log->addRoleToSteamid($p->getSteamid(), $this->getRoleFromCache("engineer"), $dt, $this->log->get_timeStart());
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "killedobject") {
          //just ignore
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "revenge") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "revenges"); 
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "player_extinguished") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "extinguishes"); 
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "chargedeployed") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "ubers"); 
	        $this->log->addRoleToSteamid($p->getSteamid(), $this->getRoleFromCache("medic"), $dt, $this->log->get_timeStart());
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "medic_death") {
	        $victim = $players[1];
	        if($this->parsingUtils->didMedicDieWithUber($logLineDetails)) {  
	          $this->log->incrementStatFromSteamid($victim->getSteamid(), "dropped_ubers");
	        }
	        $this->log->incrementStatFromSteamid($victim->getSteamid(), "healing", $this->parsingUtils->getHealing($logLineDetails));
	        $this->log->addRoleToSteamid($victim->getSteamid(), $this->getRoleFromCache("medic"), $dt, $this->log->get_timeStart());
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "healed") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "healing", $this->parsingUtils->getHealing($logLineDetails));
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "captureblocked") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "capture_points_blocked");
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "weaponstats") {
	        //superlogs specific trigger
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "flagevent") {
	        $p = $players[0];
	        $event = $this->parsingUtils->getFlagEvent($logLineDetails);
	        $this->isCtf = true;
	        if($event == "defended") {
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "flag_defends");
	          return self::GAME_CONTINUE;
	        } else if($event == "captured") {
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "flag_captures");
	          $this->log->incrementScoreForTeam($p->getTeam());
	          $this->log->addScoreChangeEvent($elapsedTime, $this->log->getBluescore(), $this->log->getRedscore());
	          return self::GAME_CONTINUE;
	        } else if($event == "picked up"
	          || $event == "dropped") {
	          //ignore
	          return self::GAME_CONTINUE;
	        }
	      }
	    }
	  } else if($this->parsingUtils->isLogLineOfType($logLine, 'Team', $logLineDetails)) {
	    $team = $this->parsingUtils->getTeamFromTeamLine($logLineDetails);
	    $teamAction = $this->parsingUtils->getTeamAction($logLineDetails);
	    if($teamAction == "triggered") {
	      $teamTriggerAction = $this->parsingUtils->getTeamTriggerAction($logLineDetails);
	      if($teamTriggerAction == "pointcaptured") {
	        $players = PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails);
	        $this->log->addUpdateUniqueStatsFromPlayerInfos($players);
	        
	        foreach($players as $p) {
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "capture_points_captured");
	        }
	        
	        $cpname = $this->parsingUtils->getCapturePointName($logLineDetails);
	        
	        $this->log->addPointCaptureEvent($elapsedTime, $players, $team, $cpname);
	        return self::GAME_CONTINUE;
	      } else if($teamTriggerAction == "Intermission_Win_Limit") {
	        return self::GAME_CONTINUE;//skip processing.
	      }
	    } else if($teamAction == "current score" || $teamAction == "final score") {
	      if($this->parsingUtils->getTeamNumberPlayers($logLineDetails) == 0) {
	        //the only time there would be zero players for a team is if something screwed up.
	        return self::GAME_APPEARS_OVER;
	      }
	      
	      if($team == "Blue") {
	        //this line will be the last of the team score lines, and will likely always be the last line in a game.
	        $this->log->addRoundStartEvent($elapsedTime, $this->log->getBluescore(), $this->log->getRedscore());
	      }
	      return self::GAME_CONTINUE;
	    }
	  }
	  
	  //still here. Did not return like expected, therefore this is an unrecognized line.
	  if(!$this->ignoreUnrecognizedLogLines) {
	    throw new UnrecognizedLogLineException($logLine);
	  }
	}
}

?>
