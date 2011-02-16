<?php

/**
 * EventPlayer filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEventPlayerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'event_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Event'), 'add_empty' => true)),
      'event_player_type' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'player_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Player'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'event_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Event'), 'column' => 'id')),
      'event_player_type' => new sfValidatorPass(array('required' => false)),
      'player_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Player'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('event_player_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EventPlayer';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'event_id'          => 'ForeignKey',
      'event_player_type' => 'Text',
      'player_id'         => 'ForeignKey',
    );
  }
}
