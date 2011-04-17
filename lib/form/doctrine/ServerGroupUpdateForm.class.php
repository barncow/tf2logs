<?php

/**
 * ServerGroupUpdate form.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ServerGroupUpdateForm extends BaseServerGroupForm {
  public function configure() {

    unset($this->widgetSchema['owner_player_id']);
    unset($this->validatorSchema['owner_player_id']);
    unset($this->widgetSchema['group_type']);
    unset($this->validatorSchema['group_type']);
    
    $max = $this->validatorSchema['slug']->getOption('max_length');
    $this->validatorSchema['slug'] = new sfValidatorAnd(array(
      new sfValidatorString(array('max_length' => $max, 'required' => true), array('required' => 'The Group URL field is required.'))
      , new sfValidatorRegex(array('pattern' => '/^([a-zA-Z0-9_\-]+)$/'), array('invalid' => 'The Group URL field is invalid. It can only contain letters, numbers, underscores (_), and dashes (-).'))
    ), array('required' => 'The Group URL field is required.'));
    $this->widgetSchema['slug']->setOption('label', 'tf2logs.com/servers/');
    
    $this->validatorSchema['name']->setOption('required', true);
    $this->validatorSchema['name']->setMessage('required', 'The Group Name field is required.');
    $this->widgetSchema['name']->setOption('label', 'Group Name');
    
    //resetting post validator to only do unique check on group slug, and keep available server validator
    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ServerGroup', 'column' => array('slug')), array('invalid' => 'The Group URL must be unique.'))
    );
  }
  
  public function setOwnerId($owner_id) {
    $this->owner_id = $owner_id;
    return $this; //for chaining
  }
}
