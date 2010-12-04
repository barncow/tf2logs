<?php
require_once('exceptions/UnrecognizedLogLineException.class.php');
require_once('exceptions/LogFileNotFoundException.class.php');

/**
 * Handles processing a log and saving the results to a database.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class LogParser {
  /**
  * Will get a raw log file from the filesystem.
  */
	public function getRawLogFile($filename) {
	  if(file_exists($filename)) {
			return file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		} else throw new LogFileNotFoundException("Could not find: ".$filename);
	}
	
	
}

?>
