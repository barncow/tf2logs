<?php
/**
Extension of sfValidatorString to clean up whitespace and ASCII control chars.
*/
class CleanStringValidator extends sfValidatorString {
  public function __construct($options = array(), $messages = array()) {

    $this->addOption('throw_global_error', false);

    parent::__construct($options, $messages);
  }
  
  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);
  }
  
  protected function doClean($value) {
    
    $clean = (string) $value;
    $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $clean);
    $clean = trim($clean);

    return parent::doClean($clean);
  }
}
?>
