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
  
  //GAME STATE CONSTANTS
  const GAME_APPEARS_OVER = 0;
  const GAME_OVER = 1;
  const GAME_CONTINUE = 2;
  
  function __construct() {
    $this->setParsingUtils(new ParsingUtils());
    $this->log = new Log();
    $this->isTournamentMode = false; //assume its not, until first world trigger round_start
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
	public function parseLogFile($filename, $logName = null, Log $logObj = null) {
	  if($logName == null) {
	    $logName = $this->parsingUtils->getNameFromFilename($filename);
	  }
	  $this->log->setName($logName);
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
	  $log->clearStats();
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
	      break; //if game is over, no need to continue processing.
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
	
	}
	
	/**
	* Do any final tasks for the line, such as scrubbing and adding the log line
	* for the saved log version.
	*/
	protected function afterParseLine($logLine) {
	  $this->log->appendToScrubbedLog($this->parsingUtils->scrubLogLine($logLine));
	}
	
	/**
	* This will do the processing of the line. This will not be called outside this
	* class. Use parseLine, since that will do some init and cleanup as needed.
	* @see public function parseLine($logLine)
	*/
	protected function doLine($logLine) {
	  $dt = $this->parsingUtils->getTimestamp($logLine);
	  if($dt === false) {
	    throw new CorruptLogLineException($logLine);
	  }
	  
	  $logLineDetails = $this->parsingUtils->getLineDetails($logLine);
	  
	  //check for world trigger, specifically the first round_start of the log. This indicates
	  //that the tournament mode has started, and pregame has ended.
	  if($this->parsingUtils->isLogLineOfType($logLine, "World triggered", $logLineDetails)) {
	    $worldTriggerAction = $this->parsingUtils->getWorldTriggerAction($logLineDetails);
	    if($worldTriggerAction == "Round_Start") {
	      $this->isTournamentMode = true;
	      if($this->log->get_timeStart() == null) {
	        //there will likely be multiple round_start's, only need the first one for the tmsp
	        $this->log->set_timeStart($dt);
	      }
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Game_Over") {
	      return self::GAME_OVER;
	    } else if($worldTriggerAction == "Round_Setup_Begin"
	      || $worldTriggerAction == "Round_Setup_End"
	      || $worldTriggerAction == "Round_Overtime"
	      || $worldTriggerAction == "Round_Win"
	      || $worldTriggerAction == "Round_Length"
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
	  || $this->parsingUtils->isLogLineOfType($logLine, "rcon from", $logLineDetails)) {
	    return self::GAME_CONTINUE; //do nothing, just add to scrubbed log
	  } else if($this->parsingUtils->isLogLineOfType($logLine, '"', $logLineDetails)) {
	    //this will be a player action line. The quote matches the quote on a player string in the log.
	    //Need to determine what action is being done here.
	    $playerLineAction = $this->parsingUtils->getPlayerLineAction($logLineDetails);
	    $players = PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails);
	    $this->log->addUpdateUniqueStatsFromPlayerInfos($players);
	    
	    if($playerLineAction == "say"
	    || $playerLineAction == "entered the game"
	    || $playerLineAction == "changed role to"
	    || $playerLineAction == "disconnected"
	    || $playerLineAction == "connected, address"
	    || $playerLineAction == "STEAM USERID validated"
	    || $playerLineAction == "say_team"
	    ) {
	      return self::GAME_CONTINUE; //do nothing, just add to scrubbed log
	    } else if($playerLineAction == "joined team") {
	      $p = $players[0];
	      $p->setTeam($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatFromPlayerInfo($p);
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "changed name to") {
	      $p = $players[0];
	      $p->setName($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatFromPlayerInfo($p);
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "killed") {
	      $attacker = $players[0];
	      $victim = $players[1];
	      $weapon = $this->parsingUtils->getWeapon($logLineDetails);
	      $this->log->incrementStatFromSteamid($attacker->getSteamid(), "kills");
	      $this->log->addWeaponToSteamid($attacker->getSteamid(), $weapon);
	      $this->log->addRoleToSteamidFromWeapon($attacker->getSteamid(), $weapon);
	      $this->log->incrementStatFromSteamid($victim->getSteamid(), "deaths"); 
	      return self::GAME_CONTINUE;
	    } else if($playerLineAction == "committed suicide with") {
	      $p = $players[0];
	      $weapon = $this->parsingUtils->getWeapon($logLineDetails);
	      $this->log->incrementStatFromSteamid($p->getSteamid(), "deaths"); 
	      $this->log->addWeaponToSteamid($p->getSteamid(), $weapon);
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
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "builtobjects"); 
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "killedobject") {
          $attacker = $players[0];
	        $objowner = $players[1];
	        if(!$attacker->equals($objowner)) {
	          //do not want to count destructions by a player destroying his own stuff
	          $this->log->incrementStatFromSteamid($attacker->getSteamid(), "destroyedobjects"); 
	        }
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
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "medic_death") {
	        if($this->parsingUtils->didMedicDieWithUber($logLineDetails)) {
	          $victim = $players[1];
	          $this->log->incrementStatFromSteamid($victim->getSteamid(), "dropped_ubers");
	        }
	        return self::GAME_CONTINUE;
	      } else if($playerLineActionDetail == "captureblocked") {
	        $p = $players[0];
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "capture_points_blocked");
	        return self::GAME_CONTINUE;
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
	        return self::GAME_CONTINUE;
	      }
	    } else if($teamAction == "current score") {
	      if($this->parsingUtils->getTeamNumberPlayers($logLineDetails) == 0) {
	        //the only time there would be zero players for a team is if something screwed up.
	        return self::GAME_APPEARS_OVER;
	      }
	      $this->log->setScoreForTeam($team, $this->parsingUtils->getTeamScore($logLineDetails));
	      return self::GAME_CONTINUE;
	    }
	  }
	  
	  //still here. Did not return like expected, therefore this is an unrecognized line.
	  throw new UnrecognizedLogLineException($logLine);
	}
}

?>
