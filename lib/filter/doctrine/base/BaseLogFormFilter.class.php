<?php

/**
 * Log filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'redscore'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bluescore'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'elapsed_time'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'game_seconds'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'map_name'            => new sfWidgetFormFilterInput(),
      'submitter_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Submitter'), 'add_empty' => true)),
      'error_log_name'      => new sfWidgetFormFilterInput(),
      'error_exception'     => new sfWidgetFormFilterInput(),
      'views'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'                => new sfValidatorPass(array('required' => false)),
      'redscore'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bluescore'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'elapsed_time'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'game_seconds'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'map_name'            => new sfValidatorPass(array('required' => false)),
      'submitter_player_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Submitter'), 'column' => 'id')),
      'error_log_name'      => new sfValidatorPass(array('required' => false)),
      'error_exception'     => new sfValidatorPass(array('required' => false)),
      'views'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('log_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Log';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'name'                => 'Text',
      'redscore'            => 'Number',
      'bluescore'           => 'Number',
      'elapsed_time'        => 'Number',
      'game_seconds'        => 'Number',
      'map_name'            => 'Text',
      'submitter_player_id' => 'ForeignKey',
      'error_log_name'      => 'Text',
      'error_exception'     => 'Text',
      'views'               => 'Number',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
    );
  }
}
