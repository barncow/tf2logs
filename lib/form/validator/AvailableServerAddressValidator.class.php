<?php
class AvailableServerAddressValidator extends sfValidatorBase {
 
  protected function configure($options = array(), $messages = array()) {
    $this->addMessage('unavailable', 'The server IP and port are already in use.');
    $this->addOption('throw_global_error', false);
  }
 
  protected function doClean($values) {
    if (null === $values) {
      $values = array();
    }
    
    if (!is_array($values)) {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $ip  = isset($values['ip']) ? $values['ip'] : null;
    $port = isset($values['port']) ? $values['port'] : null;    

    if ($ip && $port && Doctrine::getTable('Server')->isAddressUsed($ip, $port)) {
      $error = new sfValidatorError($this, 'unavailable', array(
        'ip'  => $ip,
        'port' => $port
      ));
      if ($this->getOption('throw_global_error')) {
        throw $error;
      }

      throw new sfValidatorErrorSchema($this, array('ip' => $error));
    }
    
    return $values;
  }
}
