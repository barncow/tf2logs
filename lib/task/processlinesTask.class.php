<?php

class processlinesTask extends sfBaseTask {
  protected function configure() {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'), //needed to get app.yml props
      new sfCommandOption('ip', null, sfCommandOption::PARAMETER_REQUIRED, 'IP of server'),
      new sfCommandOption('port', null, sfCommandOption::PARAMETER_REQUIRED, 'Port of server')
    ));
 
    $this->namespace = 'tf2logs';
    $this->name = 'processlines';
    $this->briefDescription = 'Collates log lines from the UDP Server and then adds a new log';
 
    $this->detailedDescription = <<<EOF
The [tf2logs:regenerate-all|INFO] task Collates log lines from the UDP Server and then adds a new log:
 
  [./symfony tf2logs:processlines --env=prod --ip=255.255.255.255 --port=27015|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $ip = $options['ip'];
    $port = $options['port'];
    
    if(!$ip || !$port) {
      throw new Exception("both IP and port not given to processlinesTask");
    }

    $logParser = new LogParser();
    $logParser->setIgnoreUnrecognizedLogLines(true);
    
    try {
      $logid = $logParser->parseAutoLog($ip, $port);
    } catch(LogFileNotFoundException $e) {
      $this->logBlock('could not find log', 'ERROR');
    } catch (Exception $ex) {
      $this->logBlock(sprintf('Error occurred while processing Log : %s', $ex), 'ERROR');
    }
  }
}
