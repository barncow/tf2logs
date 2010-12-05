<?php
require_once('exceptions/UnrecognizedLogLineException.class.php');
require_once('exceptions/LogFileNotFoundException.class.php');
require_once('exceptions/CorruptLogLineException.class.php');
require_once('exceptions/InvalidPlayerStringException.class.php');
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
  
  function __construct() {
    $this->setParsingUtils(new ParsingUtils());
    $this->log = new Log();
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
	public function parseLogFile($filename) {
	  $file = $this->getRawLogFile($filename);
	  foreach($file as $key => $logLine) {
	    $this->parseLine($logLine);
	  }
	  
	  return $this->log;
	}
	
	/**
	* This will provide a method of processing the line,
	* and being able to do some init and cleanup tasks if needed.
	* @see protected function doLine($logLine)
	*/
	public function parseLine($logLine) {
	  $exception = null;
	  try {
	    $this->doLine($logLine);
	  } catch(Exception $e) {
	    $exception = $e;
	  }
	  $this->afterParseLine($logLine);
	  if($exception != null) throw $exception;
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
	  
	  if($this->log->get_timeStart() == null) {
	    $this->log->set_timeStart($dt);
	  }
	  
	  $logLineDetails = $this->parsingUtils->getLineDetails($logLine);
	  
	  //go through line types. When complete with the line, return.
	  if($this->parsingUtils->isLogLineOfType($logLine, "Log file started", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "server_cvar: ", $logLineDetails)
	  || $this->parsingUtils->isLogLineOfType($logLine, "rcon from", $logLineDetails)) {
	    return; //do nothing, just add to scrubbed log
	  } else if($this->parsingUtils->isLogLineOfType($logLine, '"', $logLineDetails)) {
	    //this will be a player action line. The quote matches the quote on a player string in the log.
	    //Need to determine what action is being done here.
	    $playerLineAction = $this->parsingUtils->getPlayerLineAction($logLineDetails);
	    $players = PlayerInfo::getAllPlayersFromLogLineDetails($logLineDetails);
	    $this->log->addUpdateUniqueStatsFromPlayerInfos($players);
	    
	    if($playerLineAction == "say"
	    || $playerLineAction == "entered the game"
	    || $playerLineAction == "changed role to") {
	      return; //do nothing, just add to scrubbed log
	    } else if($playerLineAction == "joined team") {
	      $p = $players[0];
	      $p->setTeam($this->parsingUtils->getPlayerLineActionDetail($logLineDetails));
	      $this->log->addUpdateUniqueStatFromPlayerInfo($p);
	      return;
	    } else if($playerLineAction == "killed") {
	      $attacker = $players[0];
	      $victim = $players[1];
	      $this->log->incrementStatFromSteamid($attacker->getSteamid(), "kills");
	      $this->log->incrementStatFromSteamid($victim->getSteamid(), "deaths"); 
	      return;
	    }
	  }
	  
	  //still here. Did not return like expected, therefore this is an unrecognized line.
	  throw new UnrecognizedLogLineException($logLine);
	}
}

?>
