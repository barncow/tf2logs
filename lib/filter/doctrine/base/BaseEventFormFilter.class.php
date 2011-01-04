<?php

/**
 * Event filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEventFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'log_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => true)),
      'event_type'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'elapsed_seconds' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'attacker'        => new sfWidgetFormFilterInput(),
      'attacker_coord'  => new sfWidgetFormFilterInput(),
      'victim'          => new sfWidgetFormFilterInput(),
      'victim_coord'    => new sfWidgetFormFilterInput(),
      'assist'          => new sfWidgetFormFilterInput(),
      'assist_coord'    => new sfWidgetFormFilterInput(),
      'player_id'       => new sfWidgetFormFilterInput(),
      'text'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'log_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Log'), 'column' => 'id')),
      'event_type'      => new sfValidatorPass(array('required' => false)),
      'elapsed_seconds' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'attacker'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'attacker_coord'  => new sfValidatorPass(array('required' => false)),
      'victim'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'victim_coord'    => new sfValidatorPass(array('required' => false)),
      'assist'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'assist_coord'    => new sfValidatorPass(array('required' => false)),
      'player_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'text'            => new sfValidatorPass(array('required' => false)),
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
      'id'              => 'Number',
      'log_id'          => 'ForeignKey',
      'event_type'      => 'Text',
      'elapsed_seconds' => 'Number',
      'attacker'        => 'Number',
      'attacker_coord'  => 'Text',
      'victim'          => 'Number',
      'victim_coord'    => 'Text',
      'assist'          => 'Number',
      'assist_coord'    => 'Text',
      'player_id'       => 'Number',
      'text'            => 'Text',
    );
  }
}
