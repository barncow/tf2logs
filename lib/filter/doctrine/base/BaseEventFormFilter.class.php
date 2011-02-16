<?php

/**
 * Event filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEventFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'log_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => true)),
      'event_type'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'elapsed_seconds'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'attacker_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Attacker'), 'add_empty' => true)),
      'attacker_coord'     => new sfWidgetFormFilterInput(),
      'victim_player_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Victim'), 'add_empty' => true)),
      'victim_coord'       => new sfWidgetFormFilterInput(),
      'assist_player_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Assist'), 'add_empty' => true)),
      'assist_coord'       => new sfWidgetFormFilterInput(),
      'chat_player_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chat'), 'add_empty' => true)),
      'text'               => new sfWidgetFormFilterInput(),
      'team'               => new sfWidgetFormFilterInput(),
      'capture_point'      => new sfWidgetFormFilterInput(),
      'blue_score'         => new sfWidgetFormFilterInput(),
      'red_score'          => new sfWidgetFormFilterInput(),
      'weapon_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Weapon'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'log_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Log'), 'column' => 'id')),
      'event_type'         => new sfValidatorPass(array('required' => false)),
      'elapsed_seconds'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'attacker_player_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Attacker'), 'column' => 'id')),
      'attacker_coord'     => new sfValidatorPass(array('required' => false)),
      'victim_player_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Victim'), 'column' => 'id')),
      'victim_coord'       => new sfValidatorPass(array('required' => false)),
      'assist_player_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Assist'), 'column' => 'id')),
      'assist_coord'       => new sfValidatorPass(array('required' => false)),
      'chat_player_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Chat'), 'column' => 'id')),
      'text'               => new sfValidatorPass(array('required' => false)),
      'team'               => new sfValidatorPass(array('required' => false)),
      'capture_point'      => new sfValidatorPass(array('required' => false)),
      'blue_score'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'red_score'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'weapon_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Weapon'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('event_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Event';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'log_id'             => 'ForeignKey',
      'event_type'         => 'Text',
      'elapsed_seconds'    => 'Number',
      'attacker_player_id' => 'ForeignKey',
      'attacker_coord'     => 'Text',
      'victim_player_id'   => 'ForeignKey',
      'victim_coord'       => 'Text',
      'assist_player_id'   => 'ForeignKey',
      'assist_coord'       => 'Text',
      'chat_player_id'     => 'ForeignKey',
      'text'               => 'Text',
      'team'               => 'Text',
      'capture_point'      => 'Text',
      'blue_score'         => 'Number',
      'red_score'          => 'Number',
      'weapon_id'          => 'ForeignKey',
    );
  }
}
