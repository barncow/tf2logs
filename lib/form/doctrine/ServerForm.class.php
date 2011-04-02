<?php

/**
 * Server form.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ServerForm extends BaseServerForm {
  public function configure() {
    unset($this->widgetSchema['server_group_id']);
    unset($this->validatorSchema['server_group_id']);
    unset($this->widgetSchema['last_message']);
    unset($this->validatorSchema['last_message']);
    unset($this->widgetSchema['validate_key']);
    unset($this->validatorSchema['validate_key']);
    unset($this->widgetSchema['status']);
    unset($this->validatorSchema['status']);
    
    $this->validatorSchema['ip'] = new sfValidatorRegex(array('pattern' => '/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/'));
    $this->validatorSchema['ip']->setMessage('required', 'The IP field is required.');
    $this->validatorSchema['ip']->setMessage('invalid', 'The IP field is invalid. It must be in the form of XXX.XXX.XXX.XXX. Do not include the port.');
    
    $this->validatorSchema['port']->setMessage('required', 'The Port field is required.');
    $this->validatorSchema['port']->setMessage('invalid', 'The Port field can only be an integer value.');
    
    $this->validatorSchema['slug']->setOption('required', true);
    $this->validatorSchema['slug']->setMessage('required', 'The URL field is required.');
    $this->widgetSchema['slug']->setOption('label', 'tf2logs.com/servers/');
    
    $this->validatorSchema['name']->setOption('required', true);
    $this->validatorSchema['name']->setMessage('required', 'The Name field is required.');
    
    $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorDoctrineUnique(array('model' => 'ServerGroup', 'column' => array('slug')), array('invalid' => 'The URL must be unique.'))
      ,new AvailableServerAddressValidator()
    )));
  }
}
