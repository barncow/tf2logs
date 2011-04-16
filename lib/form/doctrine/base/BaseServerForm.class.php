<?php

/**
 * Server form base class.
 *
 * @method Server getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'slug'            => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'ip'              => new sfWidgetFormInputText(),
      'port'            => new sfWidgetFormInputText(),
      'server_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ServerGroup'), 'add_empty' => false)),
      'last_message'    => new sfWidgetFormDateTime(),
      'verify_key'      => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormInputText(),
      'current_map'     => new sfWidgetFormInputText(),
      'live_log_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('LiveLog'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'slug'            => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ip'              => new sfValidatorString(array('max_length' => 15)),
      'port'            => new sfValidatorInteger(),
      'server_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ServerGroup'))),
      'last_message'    => new sfValidatorDateTime(array('required' => false)),
      'verify_key'      => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'status'          => new sfValidatorString(array('max_length' => 1, 'required' => false)),
      'current_map'     => new sfValidatorString(array('max_length' => 25, 'required' => false)),
      'live_log_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('LiveLog'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('server[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Server';
  }

}
