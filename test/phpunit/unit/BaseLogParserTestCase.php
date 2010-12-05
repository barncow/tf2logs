<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class BaseLogParserTestCase extends sfPHPUnitBaseTestCase
{
  protected $logParser;
  protected $parsingUtils;
  protected $LFIXDIR;

  protected function _start() {
    $this->LFIXDIR = sfConfig::get('sf_test_dir')."/fixtures/LogParser/";
    $this->logParser = new LogParser();
    $this->parsingUtils = new ParsingUtils();
  }
  
  protected function _end() {
    unset($this->logParser);
    unset($this->parsingUtils);
  }
}
?>
