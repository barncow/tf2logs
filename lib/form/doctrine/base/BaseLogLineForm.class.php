<?php

/**
 * LogLine form base class.
 *
 * @method LogLine getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogLineForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'line_year'   => new sfWidgetFormInputText(),
      'line_month'  => new sfWidgetFormInputText(),
      'line_day'    => new sfWidgetFormInputText(),
      'line_hour'   => new sfWidgetFormInputText(),
      'line_minute' => new sfWidgetFormInputText(),
      'line_second' => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'server_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Server'), 'add_empty' => false)),
      'line_data'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'line_year'   => new sfValidatorInteger(),
      'line_month'  => new sfValidatorInteger(),
      'line_day'    => new sfValidatorInteger(),
      'line_hour'   => new sfValidatorInteger(),
      'line_minute' => new sfValidatorInteger(),
      'line_second' => new sfValidatorInteger(),
      'created_at'  => new sfValidatorDateTime(),
      'server_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Server'))),
      'line_data'   => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('log_line[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogLine';
  }

}
