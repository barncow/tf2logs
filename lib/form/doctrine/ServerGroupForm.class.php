<?php

/**
 * ServerGroup form.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ServerGroupForm extends BaseServerGroupForm {
  public function configure() {

    unset($this->widgetSchema['owner_id']);
    unset($this->validatorSchema['owner_id']);
    
    $max = $this->validatorSchema['slug']->getOption('max_length');
    $this->validatorSchema['slug'] = new sfValidatorAnd(array(
      new sfValidatorString(array('max_length' => $max, 'required' => true), array('required' => 'The Group URL field is required.'))
      , new sfValidatorRegex(array('pattern' => '/^([a-zA-Z0-9_\-]+)$/'), array('invalid' => 'The Group URL field is invalid. It can only contain letters, numbers, underscores (_), and dashes (-).'))
    ), array('required' => 'The Group URL field is required.'));
    $this->widgetSchema['slug']->setOption('label', 'tf2logs.com/servers/');
    
    $this->validatorSchema['name']->setOption('required', true);
    $this->validatorSchema['name']->setMessage('required', 'The Group Name field is required.');
    $this->widgetSchema['name']->setOption('label', 'Group Name');
    
    //we need to clear the post validator in the serverform to prevent server url unique check. we will re-do the validator with group url unique check, and available server check
    $serverForm = new ServerForm();
    $serverForm->validatorSchema->setPostValidator(new sfValidatorPass()); //sfValidatorPass - dummy validator
    
    $this->embedForm('server', $serverForm);
    
    //resetting post validator to only do unique check on group slug, and keep available server validator
    $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorDoctrineUnique(array('model' => 'ServerGroup', 'column' => array('slug')), array('invalid' => 'The Group URL must be unique.'))
      ,new SlugUniqueToGroupValidator()
      ,new AvailableServerAddressValidator()
    )));
  }
  
  public function setOwnerId($owner_id) {
    $this->owner_id = $owner_id;
    return $this; //for chaining
  }
  
  public function save($con = null) {
    $s = new Server();
    $values = $this->getValues();
    $s->saveNewGroupServer($values['name'], $values['slug'], $values['server']['name'], $values['server']['slug'], $values['server']['ip'], $values['server']['port'], $this->owner_id);
    return $s;
  }
}
