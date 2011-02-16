<?php

/**
 * Event form base class.
 *
 * @method Event getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEventForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'log_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => false)),
      'event_type'         => new sfWidgetFormInputText(),
      'elapsed_seconds'    => new sfWidgetFormInputText(),
      'attacker_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Attacker'), 'add_empty' => true)),
      'attacker_coord'     => new sfWidgetFormInputText(),
      'victim_player_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Victim'), 'add_empty' => true)),
      'victim_coord'       => new sfWidgetFormInputText(),
      'assist_player_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Assist'), 'add_empty' => true)),
      'assist_coord'       => new sfWidgetFormInputText(),
      'chat_player_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chat'), 'add_empty' => true)),
      'text'               => new sfWidgetFormInputText(),
      'team'               => new sfWidgetFormInputText(),
      'capture_point'      => new sfWidgetFormInputText(),
      'blue_score'         => new sfWidgetFormInputText(),
      'red_score'          => new sfWidgetFormInputText(),
      'weapon_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Weapon'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'log_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Log'))),
      'event_type'         => new sfValidatorString(array('max_length' => 10)),
      'elapsed_seconds'    => new sfValidatorInteger(),
      'attacker_player_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Attacker'), 'required' => false)),
      'attacker_coord'     => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'victim_player_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Victim'), 'required' => false)),
      'victim_coord'       => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'assist_player_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Assist'), 'required' => false)),
      'assist_coord'       => new sfValidatorString(array('max_length' => 17, 'required' => false)),
      'chat_player_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chat'), 'required' => false)),
      'text'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'team'               => new sfValidatorString(array('max_length' => 4, 'required' => false)),
      'capture_point'      => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'blue_score'         => new sfValidatorInteger(array('required' => false)),
      'red_score'          => new sfValidatorInteger(array('required' => false)),
      'weapon_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Weapon'), 'required' => false)),
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
