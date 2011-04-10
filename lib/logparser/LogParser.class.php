<?php
require_once('exceptions/UnrecognizedLogLineException.class.php');
require_once('exceptions/LogFileNotFoundException.class.php');
require_once('exceptions/CorruptLogLineException.class.php');
require_once('exceptions/InvalidPlayerStringException.class.php');
require_once('exceptions/TournamentModeNotFoundException.class.php');
require_once('exceptions/NoDataInLogFileException.class.php');
require_once('ParsingUtils.class.php');
require_once('PlayerInfo.class.php');
require_once('LogSave.class.php');

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
  protected $previousLogLine;
  protected $gameOver;
  protected $isServerCvars;
  protected $minutesIntervals;
  protected $overwriteMapName;
  protected $server;
  
  //these values are used to detect whether a round_win is valid or not.
  protected $currentRoundWinTeam;
  protected $prevBlueTeamScore;
  protected $prevRedTeamScore;
  
  //GAME STATE CONSTANTS
  const GAME_APPEARS_OVER = 0;
  const GAME_OVER = 1;
  const GAME_CONTINUE = 2;
  
  function __construct() {
    $this->setParsingUtils(new ParsingUtils());
    $this->buildWeaponCache();
    $this->buildRoleCache();
    $this->ignoreUnrecognizedLogLines = sfConfig::get('app_ignore_unrecognized_log_lines');
    $this->clearValues();
  }
  
  protected function clearValues() {
    $this->log = new Log();
    $this->isTournamentMode = false; //assume its not, until first world trigger round_start
    $this->isCtf = false;
    $this->addToScrubbedLog = true;
    $this->gameOver = false;
    $this->isServerCvars = false;
    $this->prevBlueTeamScore = 0;
    $this->prevRedTeamScore = 0;
    //will use assoc. array of steamids to determine unique team switches, instead of one player switching multi times.
    $this->playerChangeTeams = array(); 
    $this->previousLogLine = null;
    $this->minutesIntervals = array();
    $this->overwriteMapName = false;
    $this->server = null;
  }
  
  public function setIgnoreUnrecognizedLogLines($ignoreUnrecognizedLogLines) {
    $this->ignoreUnrecognizedLogLines = $ignoreUnrecognizedLogLines;
  }
  
  public function buildWeaponCache() {
    $weps = Doctrine::getTable('Weapon')->getAllWeapons();
    foreach($weps as $wep) {
      $this->weapons[$wep->getKeyName()] = $wep;
    }
  }
  
  public function buildRoleCache() {
    $rs = Doctrine::getTable('Role')->findAll();
    foreach($rs as $r) {
      $this->roles[$r->getKeyName()] = $r;
    }
  }
  
  /**
  * This will find a weapon from the weapon cache. If not found, one will be created
  * and saved to the database, will be added to the cache, and returned.
  */
  public function getWeaponFromCache($keyName) {
    if($keyName === false) return false;
    if(isset($this->weapons[$keyName])) {
      return $this->weapons[$keyName];
    }
    
    $wep = Doctrine::getTable('Weapon')->findOneByKeyName($keyName);
    if($wep) {
      //the weapon was added after the cache was created. Add the weapon to our cache.
      $this->weapons[$keyName] = $wep;
      return $wep;
    }
    
    //still here, need to create new weapon
    $wep = new Weapon();
    $wep->setKeyName($keyName);
    //setting the weapon name to keyname for easier sorting onscreen.
    $wep->setName($keyName);
    $wep->save();
    $this->weapons[$keyName] = $wep;
    return $wep;
  }
  
  /**
  * This will find a role from the role cache. This will not change, so it will not add.
  */
  public function getRoleFromCache($keyName) {
    if($keyName === false) return false;
    return $this->roles[$keyName];
  }
  
  public function &getLog() {
    return $this->log;
  }
  
  public function setParsingUtils($parsingUtils) {
    $this->parsingUtils = $parsingUtils;
  }
  
  public function getParsingUtils() {
    return $this->parsingUtils;
  }
  
  public function isGameOver() {
    return $this->gameOver;
  }
  
  /**
  * Returns the elapsed time in milliseconds since the beginning of the match.
  */
  public function getElapsedTime($now) {
    if($this->log->get_timeStart() == null) return 0;
    return $now->getTimestamp()-$this->log->get_timeStart()->getTimestamp();
  }
  
  /**
  * Returns the amount of actual gameplay seconds: elapsed_time - pause time - half time
  */
  public function getGameSeconds() {
    if(count($this->minutesIntervals) == 0) return 0;
    
    $lastStartDt = null;
    $seconds = 0;
    foreach($this->minutesIntervals as $mi) {
      if($mi['type'] == 'start') {
        $lastStartDt = $mi['dt'];
      } else if($mi['type'] == 'stop') {
        if($lastStartDt) {
          //windifference gameovers will output game_over twice, thus resulting in two stops, and a null $lastStartDt
          //just ignore any stop that does not have a start.
          $dt = $mi['dt'];
          $seconds += $dt->getTimestamp()-$lastStartDt->getTimestamp();
          $lastStartDt = null;
        }
      }
    }
    
    if($lastStartDt) {
      //since stop intervals will null lastStartDt (causing false value), if lastStartDt has a value, a stop was not called.
      //use currentDt as a stop.
      $seconds += $this->currentDt->getTimestamp()-$lastStartDt->getTimestamp();
    }
    
    return $seconds;
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
	  $tlp = sfTimerManager::getTimer('totalLogParse');
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
	  
	  $ret;
	  try {
	    $ret =  $this->parseLog($file);
	  } catch(TournamentModeNotFoundException $e) {
	    //if no tournament mode was found, re-run entire log file as one round.
	    return $this->parseLog($file, true);
	  }
	  $tlp->addTime();
	  return $ret;
	}
	
	/**
	* This will parse the entire log file saved from the database.
	*/
	public function parseLogFromDB($logid) {  
	  $this->clearValues(); //during regen, we only use one log parser - need to clear state for next parsing.
	  $this->log = Doctrine::getTable('Log')->getLogForRegenerationById($logid);
	  if(!$this->log) {
	    throw new LogFileNotFoundException("Could not find the log ID: ".$logid);
	  }
	  $file = explode("\n", Doctrine::getTable('LogFile')->findOneByLogId($logid)->getLogData());
	  $this->log->clearLog();
	  
	  $logid;
	  try {
	    $logid =  $this->parseLog($file);
	  } catch(TournamentModeNotFoundException $e) {
	    //if no tournament mode was found, re-run entire log file as one round.
	    $logid = $this->parseLog($file, true);
	  }
	  
	  $this->log->free(true);
	  unset($this->log);
	  return $logid;
	}
	
	/**
	  This will gather lines for a server from LogLine, and use those to parse the log.
	*/
	public function parseAutoLog($ip, $port) {
	  $tlp = sfTimerManager::getTimer('totalAutoLogParse');
	  
	  $server = Doctrine::getTable('Server')->findActiveServer($ip, $port);
	  
	  $this->log->setName('Name');
	  $this->log->setMapName($server->getCurrentMap());
	  $this->log->setSubmitterPlayerId($server->getServerGroup()->getOwnerPlayerId());
	  $this->log->setIsAuto(true);
	  
	  $lines = Doctrine::getTable('LogLine')->getLogLinesForServer($ip, $port);
	  
	  $ret;
	  try {
	    $ret =  $this->parseLog($lines);
	  } catch(TournamentModeNotFoundException $e) {
	    //if no tournament mode was found, re-run entire log file as one round.
	    return $this->parseLog($lines, true);
	  }
	  $tlp->addTime();
	  
	  //update server from Processing Status to Active status.
	  Doctrine::getTable('Server')->endProcessingStatus($ip, $port);
	  
	  return $ret;
	}
	
	/**
	  Use during generation of a log through the udp server.
	  Will get any lines older than $delay, and then run them through the parser
	  The results will be saved to the database.
	  $newLines is an array of strings of the log line(s) to parse.
	  This is also related to parseLog - make sure functionality changes are propagated accordingly.
	*/
	public function parseNewLines($newLines, $ip, $port) {
	  //note - do not run clear values since state needs to be maintained between calls of this function.
	  
	  if(!$this->server) {
	    //server not set, so this is our first time through. Get the server info, and init the log.
	    $this->server = Doctrine::getTable('Server')->findActiveServer($ip, $port);
	    if(!$this->server) throw new Exception("Server could not be found for $ip:$port in parseNewLines");
	  
	    $this->log->setName('Namenewlines');
	    $this->log->setMapName($this->server->getCurrentMap());
	    $this->log->setSubmitterPlayerId($this->server->getServerGroup()->getOwnerPlayerId());
	    $this->log->setIsAuto(true);
	    $this->setIgnoreUnrecognizedLogLines(true);
	  }
	  
	  foreach($newLines as $logLine) {
	    $game_state;
	    try {
	      $game_state = $this->parseLine($logLine);
	    } catch(CorruptLogLineException $clle) {
	      //just pitch the line and move on
	    }
	    
	    if($game_state === self::GAME_APPEARS_OVER) {
	      $this->gameOver = true;
	      break; //do not keep going as the log did not exit cleanly
	    } else if ($game_state === self::GAME_OVER) {
	      //mark as game over, but keep processing in case there is another round_start.
	      $this->gameOver = true;
	    }
	  }
	  
	  //todo - need to make sure that this will always get called somehow - if not explicitly then set a timeout and finish this ////////////////////////////////////////////////////////////////////////////////////////////
	  if($this->gameOver) {
	    //check to see if any data was even entered.
      if(count($this->log->Stats) == 0) {
        $this->log->delete();
        return; //nothing more to do
      }
      
      $this->finishLog();
    }
    
	  $ls = new LogSave();
	  $this->log = $ls->save($this->log, true);
	  return $this->log->getId();
	}
	
	/**
	  Parse through complete log file.
	  This is also related to parseNewLines - make sure functionality changes are propagated accordingly.
	*/
	protected function parseLog($arrayLogLines, $ignoreTourneyModeNotFound = false) {
	  $game_state = null;
	  $fileLength = count($arrayLogLines);
	  
	  if($ignoreTourneyModeNotFound) {
	    //since ignoring trying to find the actual start of the match, we will just set tournamentmode = true immediately.
	    $this->isTournamentMode = true;
	  }
	  

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
	      //just pitch the line and move on
	    }
	    
	    if($game_state == self::GAME_APPEARS_OVER) {
	      break; //do not keep going as the log did not exit cleanly
	    } else if ($game_state == self::GAME_OVER) {
	      //mark as game over, but keep processing in case there is another round_start.
	      $this->gameOver = true;
	    }
	  }
	  if(!$this->isTournamentMode) {
	    throw new TournamentModeNotFoundException();
	  }
	  
	  //check to see if any data was even entered.
    if(count($this->log->Stats) == 0) {
      throw new NoDataInLogFileException();
    }
    
    $this->finishLog();
    $st = sfTimerManager::getTimer('saveLog');
	  $ls = new LogSave();
	  $id = $ls->save($this->log);
	  $st->addTime();
	  return $id;
	}
	
	/**
	* This will provide a method of processing the line,
	* and being able to do some init and cleanup tasks if needed.
	* @see protected function doLine($logLine)
	*/
	public function parseLine($logLine) {
	  $exception = null;
	  $game_state = null;
	  $logLine = $this->beforeParseLine($logLine);
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
	  
	  //calc game seconds
	  $this->log->setGameSeconds($this->getGameSeconds());
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
	  
	  $this->previousLogLine = $logLine;
	}
	
	/**
	  Function to be called before doing processing to do some cleanup of the text.
	*/
	protected function beforeParseLine($logLine) {
	  //doing a ltrim here to remove any extraneous characters that may corrupt the log.
	  $logLine = substr($logLine, strpos($logLine, "L"));
	  
	  //this will remove any invalid utf-8 chars
	  $logLine = $this->removeIllegalChars($logLine);
    
    return $logLine;
	}
	
	/**
	  Provided by:
	  http://webcollab.sourceforge.net/unicode.html
	*/
	protected function removeIllegalChars($logLine) {   
   preg_match_all('/([\x09\x0a\x0d\x20-\x7e]'. // ASCII characters
     '|[\xc2-\xdf][\x80-\xbf]'. // 2-byte (except overly longs)
     '|\xe0[\xa0-\xbf][\x80-\xbf]'. // 3 byte (except overly longs)
     '|[\xe1-\xec\xee\xef][\x80-\xbf]{2}'. // 3 byte (except overly longs)
     '|\xed[\x80-\x9f][\x80-\xbf])+/', // 3 byte (except UTF-16 surrogates)
     $logLine, $clean_pieces );

    $logLine = join('?', $clean_pieces[0] );
    return $logLine;
	}
	
	protected function addMinutesInterval($type, $dt) {
	  $this->minutesIntervals[] = array('type'=>$type,'dt'=>$dt);
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
	  $this->currentDt = $dt;
	  
	  $logLineDetails = $this->parsingUtils->getLineDetails($logLine);
	  
	  //always set elapsed time. When the log finishes, the last timestamp set will be our elapsed time.
	  $elapsedTime = $this->getElapsedTime($dt);
	  $this->log->setElapsedTime($elapsedTime);
	  
	  
	  if($this->log->get_timeStart() == null && $this->isTournamentMode) {
      //we do not yet have a timestart, and we are in tournament mode. This should only happen 
      //when we put ourselves manually in tournament mode to ignore the
      //tournament mode not found exception.
      $this->log->set_timeStart($dt);
      
      //adding a round start event for the first round.
      $this->log->addRoundStartEvent($elapsedTime, 0, 0);
      
      $this->addMinutesInterval('start', $dt);
    }
    
    if ($this->parsingUtils->isLogLineOfType($logLine, "server cvars start", $logLineDetails)) {
	    $this->isServerCvars = true;
	  } else if($this->parsingUtils->isLogLineOfType($logLine, "server cvars end", $logLineDetails)) {
	    $this->isServerCvars = false;
	    return self::GAME_CONTINUE;
	  }
	  if($this->isServerCvars) {
	    //tf2 will sometimes dump out a whole block of cvars. Want to ignore these.
	    return self::GAME_CONTINUE;
	  }
	  
	  //check for world trigger, specifically the first round_start of the log. This indicates
	  //that the tournament mode has started, and pregame has ended.
	  if($this->parsingUtils->isLogLineOfType($logLine, "World triggered", $logLineDetails)) {
	    $worldTriggerAction = $this->parsingUtils->getWorldTriggerAction($logLineDetails);
	    if($worldTriggerAction == "Round_Start") {
	      if($this->gameOver) {
	        $this->addMinutesInterval('start', $dt);
	        $this->gameOver = false;
	      }
	      $this->isTournamentMode = true;
	      if($this->log->get_timeStart() == null) {
	        //there will likely be multiple round_start's, only need the first one for the tmsp
	        $this->log->set_timeStart($dt);
	        
	        //adding a round start event for the first round.
	        $this->log->addRoundStartEvent($elapsedTime, 0, 0);
	        
	        $this->addMinutesInterval('start', $dt);
	      }
	      
	      $this->playerChangeTeams = array(); //resetting change teams. assuming players are at where they need to be.
	      
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Game_Over") {
	      $this->addMinutesInterval('stop', $dt);
	      return self::GAME_OVER;
	    } else if($worldTriggerAction == "Round_Win") {
	      //find the team that won the round. Scores will be updated later when this round is determined to be valid.
	      if(!$this->isCtf) {
	        $this->currentRoundWinTeam = $this->parsingUtils->getRoundWinTeam($logLineDetails);
	      }
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Game_Paused") {
	      $this->addMinutesInterval('stop', $dt);
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Game_Unpaused") {
	      $this->addMinutesInterval('start', $dt);
	      return self::GAME_CONTINUE;
	    } else if($worldTriggerAction == "Round_Setup_Begin"
	      || $worldTriggerAction == "Round_Setup_End"
	      || $worldTriggerAction == "Round_Overtime"
	      || $worldTriggerAction == "Round_Length"
	      || $worldTriggerAction == "Mini_Round_Selected" //not sure what mini rounds are (discovered in 1911 log)
	      || $worldTriggerAction == "Mini_Round_Start"
	      || $worldTriggerAction == "Mini_Round_Length"
	      || $worldTriggerAction == "Mini_Round_Win"
	      || $worldTriggerAction == "Round_Stalemate"
	      ) {
	      return self::GAME_CONTINUE; //no need to process
	    }
	  } else if($this->parsingUtils->isLogLineOfType($logLine, "rcon from", $logLineDetails)) {
	    //this could contain sensitive information. Do not add it to the log.
	    $this->addToScrubbedLog = false;
	    return self::GAME_CONTINUE;
	  } else if ($this->parsingUtils->isLogLineOfType($logLine, "Loading map ", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "Started map ", $logLineDetails)) {
	    //populate the map field if not specified.
	    $map = $this->parsingUtils->getMapFromMapLine($logLineDetails);
	    if(!$this->log->getMapName()) $this->log->setMapName($map);
	    return self::GAME_CONTINUE;
	  } 
	  
	  if(!$this->isTournamentMode) {
	    return self::GAME_CONTINUE; //do not want to track information when not in tournament mode.
	  }
	  
	  //go through line types. When complete with the line, return.
	  if($this->parsingUtils->isLogLineOfType($logLine, "Log file started", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "server_cvar: ", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "server_message: ", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "Your server will be restarted on map change.", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "[META]", $logLineDetails)
	  ) {
	    return self::GAME_CONTINUE; //do nothing, just add to scrubbed log
	  } else if ($this->parsingUtils->isLogLineOfType($logLine, "Log file closed", $logLineDetails)) {
	    return self::GAME_APPEARS_OVER;
	  } else if($this->parsingUtils->isLogLineOfType($logLine, '"', $logLineDetails)) {
	    //this will be a player action line. The quote matches the quote on a player string in the log.
	    
	    if(strpos($logLine, "><BOT><")) {
	      //ignoring all bots.
	      return self::GAME_CONTINUE;
	    }
	    
	    //Need to determine what action is being done here.
	    $playerLineAction = $this->parsingUtils->getPlayerLineAction($logLineDetails);
	    $players = PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails);
	    $this->log->addUpdateUniqueStatsFromPlayerInfos($players);
	    
	    //want to process any joined team line whether or not we are in game over.
	    //otherwise, skip the rest if in gameover.
	    if($playerLineAction == "joined team") {
	      $p = $players[0];
	      $p->setTeam($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatsFromPlayerInfos(array($p));
	      $this->playerChangeTeams[$p->getSteamid()] = 1;
	      if(count($this->playerChangeTeams) >= count($this->log->getStats())) {
	        //teams have switched, switch scores
	        $this->log->switchScores();
	        $tempblue = $this->prevBlueTeamScore;
	        $this->prevBlueTeamScore = $this->prevRedTeamScore;
	        $this->prevRedTeamScore = $tempblue;
	        
	        $this->log->addScoreChangeEvent($elapsedTime, $this->log->getBluescore(), $this->log->getRedscore());
	        $this->playerChangeTeams = array();
	      }
	      return self::GAME_CONTINUE;
	    } else if($this->gameOver) {
	      return self::GAME_OVER; //prevent unrecogloglineexception
	    } else {
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
	        $attackercoords = $this->parsingUtils->getKillCoords("attacker", $logLineDetails);
	        $victimcoords = $this->parsingUtils->getKillCoords("victim", $logLineDetails);
	        
	        if(!$attackercoords || !$victimcoords) {
	          throw new CorruptLogLineException($logLine);
	        }
	        
	        if($ck == "feign_death") {
	          //do not alter stats for dead ringer spies.
	          return self::GAME_CONTINUE;
	        }
	        
	        if(($w == "sniperrifle" || $w == "tf_projectile_arrow" || $w == "ambassador") && $ck == "headshot") {
	          //if a headshot occurred, edit the weapon to be the normal weapon's headshot variant.
	          //also, add headshot score
	          $w .= "_hs";
	          $this->log->incrementStatFromSteamid($attacker->getSteamid(), "headshots");
	        } else if($ck == "backstab") {
	          //edit weapon to be backstab variant
	          $w .= "_bs";
	          $this->log->incrementStatFromSteamid($attacker->getSteamid(), "backstabs");
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
	        
	        
	        $this->log->addKillEvent($elapsedTime, $weapon->getId(), $attacker->getSteamid(), $attackercoords
	          ,$victim->getSteamid(), $victimcoords);
	        
	        return self::GAME_CONTINUE;
	      } else if($playerLineAction == "committed suicide with") {
	        $p = $players[0];
	        $weapon = $this->getWeaponFromCache($this->parsingUtils->getWeapon($logLineDetails));
	        $this->log->incrementStatFromSteamid($p->getSteamid(), "deaths"); 
	        $this->log->incrementWeaponForPlayer($p->getSteamid(), $weapon, 'deaths');
	        $this->log->addPlayerStatToSteamid($p->getSteamid(), $p->getSteamid(), "deaths");
	        if($weapon->getRole() != null) $this->log->addRoleToSteamid($p->getSteamid(), $weapon->getRole(), $dt, $this->log->get_timeStart());
	        return self::GAME_CONTINUE;
	      } else if ($playerLineAction == "picked up item") {
	        $p = $players[0];
	        $itemKeyName = $this->parsingUtils->getPickedUpItemKeyName($logLineDetails);
	        $this->log->addItemPickupStatToSteamid($p->getSteamid(), $itemKeyName);
	        return self::GAME_CONTINUE;
	      } else if($playerLineAction == "triggered") {	      
	        $playerLineActionDetail = $this->parsingUtils->getPlayerLineActionDetail($logLineDetails);
	        
	        if($playerLineActionDetail == "kill assist") {
	          //this line is a complement to a previous line. Do not increment the victim's death; it was done above.
	          if($this->parsingUtils->getCustomKill($this->previousLogLine) == "feign_death") {
	            //do not mark an assist if the assist is for a DR kill.
	            return self::GAME_CONTINUE;
	          }
	          $p = $players[0];
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "assists"); 
	          $this->log->markLastKillEventWithAssist($p->getSteamid(), $this->parsingUtils->getKillCoords("assister", $logLineDetails));
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "domination") {
	          $attacker = $players[0];
	          $victim = $players[1];
	          $this->log->incrementStatFromSteamid($attacker->getSteamid(), "dominations"); 
	          $this->log->incrementStatFromSteamid($victim->getSteamid(), "times_dominated"); 
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "builtobject") {
	          $p = $players[0];
	          //spies attaching sappers are actually builtobject sappers. Filter these out.
	          if($this->parsingUtils->getObjectFromBuiltObject($logLineDetails) != "OBJ_ATTACHMENT_SAPPER") {
	            $this->log->addRoleToSteamid($p->getSteamid(), $this->getRoleFromCache("engineer"), $dt, $this->log->get_timeStart());
	          } else {
	            $this->log->addRoleToSteamid($p->getSteamid(), $this->getRoleFromCache("spy"), $dt, $this->log->get_timeStart());
	          }
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "killedobject") {
	          return self::GAME_CONTINUE; //ignoring all together, weapons not reliable.
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
	          $healer = $players[0];
	          if(count($players) == 2) {
	            //supp stats plugin will have two players for a healed line. SuperLogs only has the one player.
	            $healee = $players[1]; //person being healed
	            $this->log->addPlayerHealStatToSteamid($healer->getSteamid(), $healee->getSteamid(), $this->parsingUtils->getHealing($logLineDetails));
	          } else {
	            $this->log->incrementStatFromSteamid($healer->getSteamid(), "healing", $this->parsingUtils->getHealing($logLineDetails));
	          }
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "captureblocked") {
	          $p = $players[0];
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "capture_points_blocked");
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "weaponstats") {
	          //superlogs specific trigger
	          return self::GAME_CONTINUE;
	        } else if($playerLineActionDetail == "damage") {
	          //cinq's damage log
	          $p = $players[0];
	          $this->log->incrementStatFromSteamid($p->getSteamid(), "damage", $this->parsingUtils->getDamage($logLineDetails));
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
          $this->addMinutesInterval('stop', $dt);
          return self::GAME_APPEARS_OVER;
        }
        
        $score = (int)$this->parsingUtils->getTeamScore($logLineDetails);
        
        /*
        since the team score lines can reset scores to zero, either because of two half logs or
        normal map operation, can't use them for scores. CTF maps just count flagcaptures, so
        no special attention needed. Need to use round_win, however these can be awarded if no
        person capped last point (ie. round cut short due to timelimit). Need to filter these out.
        The only way a round_win is legit, is if *any* team's score changes, even if it gets reset to zero.
        (since a/d maps switch teams, and scores switch before teams, any score could change; just award win
        to team in currentRoundWinTeam in either case.)
        */
        if(!$this->isCtf) {
          if($team == "Blue") {
            if($score != $this->prevBlueTeamScore) {
              if($this->currentRoundWinTeam) {
                $this->log->incrementScoreForTeam($this->currentRoundWinTeam);
                $this->currentRoundWinTeam = null;
              }
            }
            $this->prevBlueTeamScore = $score;
          } else if($team == "Red") {
            if($score != $this->prevRedTeamScore) {
              if($this->currentRoundWinTeam) {
                $this->log->incrementScoreForTeam($this->currentRoundWinTeam);
                $this->currentRoundWinTeam = null;
              }
            }
            $this->prevRedTeamScore = $score;
          }
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
	  } else {
	    return self::GAME_CONTINUE;
	  }
	}
}

?>
