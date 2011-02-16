<?php

/**
 * EventPlayer form base class.
 *
 * @method EventPlayer getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEventPlayerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'event_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Event'), 'add_empty' => false)),
      'event_player_type' => new sfWidgetFormInputText(),
      'player_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Player'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Event'))),
      'event_player_type' => new sfValidatorString(array('max_length' => 1)),
      'player_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Player'))),
    ));

    $this->widgetSchema->setNameFormat('event_player[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EventPlayer';
  }

}
