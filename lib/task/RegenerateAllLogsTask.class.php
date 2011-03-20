<?php
/**
This will regenerate all logs. BE SURE TO DO A MANUAL BACKUP BEFORE RUNNING!!!!
$ php symfony tf2logs:regenerate-all --env=prod
*/
class RegenerateAllLogsTask extends sfBaseTask {
  protected function configure() {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('start', null, sfCommandOption::PARAMETER_OPTIONAL, 'ID of Log to Start with', 1),
      new sfCommandOption('end', null, sfCommandOption::PARAMETER_OPTIONAL, 'ID of Log to End with', 'max'),
      new sfCommandOption('gc', null, sfCommandOption::PARAMETER_OPTIONAL, 'Number of logs to do garbage collection', 10)
    ));
 
    $this->namespace = 'tf2logs';
    $this->name = 'regenerate-all';
    $this->briefDescription = 'Regenerates all Logs';
 
    $this->detailedDescription = <<<EOF
The [tf2logs:regenerate-all|INFO] task regenerates all logs from the LogFile table:
 
  [./symfony tf2logs:regenerate-all --env=prod --start=1 --end=max|INFO]
  
  BE SURE THAT YOU HAVE PERFORMED A MANUAL BACKUP BEFORE RUNNING THIS TASK!!!
EOF;
  }
 
  protected function execute($arguments = array(), $options = array()) {
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    if($this->askConfirmation("Are you sure that you ran a manual backup before running this task?", 'QUESTION', false)) {
      //only want to continue if a backup was done.
      
      $this->logBlock('NOTICE - THIS EATS UP CPU DURING ITS PROCESSING - MAY WANT TO ONLY DO THIS ON OFF-HOURS', 'INFO_LARGE');
      
      $logTable = Doctrine_Core::getTable('Log');
      $i = $options['start'];
      $count = 0;
      
      $end = $options['end'];
      if($end == 'max') {
        $maxid = $logTable->getMaxLogId();
      } else {
        $maxid = $end;
      }
      
      $this->logBlock(sprintf('Regenerating %d logs', $maxid-$i+1), 'INFO_LARGE');
      
      gc_enable();
      
      //start from 1 and go to maxid, and regenerate all logs. If log is not found, ignore it and move on.
      while($i <= $maxid) {
        $logParser = new LogParser();
        $logParser->setIgnoreUnrecognizedLogLines(true);
      
        $logid;
        $regenSuccess = true;
        
        try {
          $logid = $logParser->parseLogFromDB($i);
        } catch(LogFileNotFoundException $e) {
          $this->logSection('regenerate', sprintf('Log ID: %d could not be found - skipping', $i), null, 'ERROR');
          $regenSuccess = false;
        } catch (Exception $ex) {
          $this->logBlock(sprintf('Error occurred while processing Log ID: %d - %s', $i, $ex), 'ERROR_LARGE');
          $regenSuccess = false;
          //return; //go no further
        }
        
        if($end == 'max') {
          $maxid = $logTable->getMaxLogId();
        } else {
          $maxid = $end;
        }
        
        if($regenSuccess) {
          $this->logSection('regenerate', sprintf('Regenerated Log ID: %d of %d', $logid, $maxid));
        }
        
        if($i % $options['gc'] == 0) {
          //every 10 logs we want to invoke garbage collection.
          $this->logSection('gc', 'Running garbage collection');
          gc_collect_cycles();
        }
        
        ++$i;
        ++$count;
        unset($logParser);
      }
      
      $this->logSection('clearcache', 'Going to clear the template cache.');
      $this->runTask('cache:clear', array('--type=template', '--env='.$options['env']));
      
      $this->logBlock(sprintf('Regeneration complete. Regenerated %d logs.', $count), 'INFO_LARGE');
      $this->logBlock('You will probably want to do another manual backup right now.', 'INFO_LARGE');
    } else {
      $this->logBlock('You must perform a manual backup before performing this operation.', 'ERROR_LARGE');
    }
  }
}
