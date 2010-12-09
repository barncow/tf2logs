<?php

/**
 * LogFile filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogFileFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'log_data' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'log_data' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('log_file_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogFile';
  }

  public function getFields()
  {
    return array(
      'log_id'   => 'Number',
      'log_data' => 'Text',
    );
  }
}
