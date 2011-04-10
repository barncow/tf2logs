<?php

class LineByLineTask extends sfBaseTask {
  protected function configure() {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'), //needed to get app.yml props
      new sfCommandOption('ip', null, sfCommandOption::PARAMETER_REQUIRED, 'IP of server'),
      new sfCommandOption('port', null, sfCommandOption::PARAMETER_REQUIRED, 'Port of server')
    ));
 
    $this->namespace = 'tf2logs';
    $this->name = 'linebyline';
    $this->briefDescription = 'Collates log lines from the UDP Server and then adds a new log as it is generated';
 
    $this->detailedDescription = <<<EOF
The [tf2logs:linebyline|INFO] task Collates log lines from the UDP Server and then adds a new log as it is generated. Should be used on first round_start:
 
  [./symfony tf2logs:linebyline --env=prod --ip=255.255.255.255 --port=27015|INFO]
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

    $lineByLineParser = new LineByLineParser($ip, $port);
    $lineByLineParser->start();
  }
}
