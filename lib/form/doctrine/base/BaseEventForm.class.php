<?php

/**
 * Event form base class.
 *
 * @method Event getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEventForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'log_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => false)),
      'event_type'      => new sfWidgetFormInputText(),
      'elapsed_seconds' => new sfWidgetFormInputText(),
      'attacker'        => new sfWidgetFormInputText(),
      'attacker_coord'  => new sfWidgetFormInputText(),
      'victim'          => new sfWidgetFormInputText(),
      'victim_coord'    => new sfWidgetFormInputText(),
      'assist'          => new sfWidgetFormInputText(),
      'assist_coord'    => new sfWidgetFormInputText(),
      'player_id'       => new sfWidgetFormInputText(),
      'text'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'log_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Log'))),
      'event_type'      => new sfValidatorString(array('max_length' => 10)),
      'elapsed_seconds' => new sfValidatorInteger(),
      'attacker'        => new sfValidatorInteger(array('required' => false)),
      'attacker_coord'  => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'victim'          => new sfValidatorInteger(array('required' => false)),
      'victim_coord'    => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'assist'          => new sfValidatorInteger(array('required' => false)),
      'assist_coord'    => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'player_id'       => new sfValidatorInteger(array('required' => false)),
      'text'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('event[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Event';
  }

}
