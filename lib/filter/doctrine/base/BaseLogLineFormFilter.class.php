<?php

/**
 * LogLine filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogLineFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'line_year'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'line_month'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'line_day'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'line_hour'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'line_minute' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'line_second' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'server_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Server'), 'add_empty' => true)),
      'line_data'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'line_year'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'line_month'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'line_day'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'line_hour'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'line_minute' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'line_second' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'server_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Server'), 'column' => 'id')),
      'line_data'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('log_line_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogLine';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'line_year'   => 'Number',
      'line_month'  => 'Number',
      'line_day'    => 'Number',
      'line_hour'   => 'Number',
      'line_minute' => 'Number',
      'line_second' => 'Number',
      'created_at'  => 'Date',
      'server_id'   => 'ForeignKey',
      'line_data'   => 'Text',
    );
  }
}
