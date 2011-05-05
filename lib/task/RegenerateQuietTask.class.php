<?php
/**
This will regenerate all logs. BE SURE TO DO A MANUAL BACKUP BEFORE RUNNING!!!!
$ php symfony tf2logs:regenerate-all --env=prod
*/
class RegenerateQuietTask extends sfBaseTask {
  protected function configure() {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('ids', null, sfCommandOption::PARAMETER_OPTIONAL, 'IDs to regenerate', '')
    ));
 
    $this->namespace = 'tf2logs';
    $this->name = 'regenerate-quiet';
    $this->briefDescription = 'Regenerates Logs - Only outputs on success or error.';
 
    $this->detailedDescription = <<<EOF
The [tf2logs:regenerate-quiet|INFO] task regenerates logs, and only reports success or failure of regenerating a log. 
No prompts or excess functionality (like clearing cache).
 
  [./symfony tf2logs:regenerate-quiet --env=prod --ids=1,2 |INFO]
EOF;
  }
 
  protected function execute($arguments = array(), $options = array()) {
    $databaseManager = new sfDatabaseManager($this->configuration);
      
    $logTable = Doctrine_Core::getTable('Log');
    
    $ids = split(',', $options['ids']);
    
    foreach($ids as $id) {
      $logParser = new LogParser();
      $logParser->setIgnoreUnrecognizedLogLines(true);
    
      $logid;
      $regenSuccess = true;
      
      try {
        $logid = $logParser->parseLogFromDB($id);
      } catch(LogFileNotFoundException $e) {
        $this->log(sprintf('Log ID: %d could not be found - skipping', $id));
        $regenSuccess = false;
      } catch (Exception $ex) {
        $this->log(sprintf('Error occurred while processing Log ID: %d - %s', $id, $ex));
        $regenSuccess = false;
        //return; //go no further
      }
      
      if($regenSuccess) {
        $this->log(sprintf('Regenerated Log ID: %d', $logid));
      }
      
      unset($logParser);
      flush(); //flushing buffer so that it is not waiting for all logs to finish before sending.
    }
  }
}
