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
    $isEmbed = false;
    if(!$ip && !$port) {
      //handling ServerForm embed
      $ip  = isset($values['server']['ip']) ? $values['server']['ip'] : null;
      $port = isset($values['server']['port']) ? $values['server']['port'] : null;
      $isEmbed = true;
    }    

    if ($ip && $port && Doctrine::getTable('Server')->isAddressUsed($ip, $port)) {
      $error;
      if($isEmbed) {
        $error = new sfValidatorError($this, 'unavailable', array(
          'server_ip'  => $ip,
          'server_port' => $port
        ));
      } else {
        $error = new sfValidatorError($this, 'unavailable', array(
          'ip'  => $ip,
          'port' => $port
        ));
      }
      
      if ($this->getOption('throw_global_error')) {
        throw $error;
      }
      
      if($isEmbed) {
        throw new sfValidatorErrorSchema($this, array('server_ip' => $error));
      } else {
        throw new sfValidatorErrorSchema($this, array('ip' => $error));
      }
    }
    
    return $values;
  }
}
