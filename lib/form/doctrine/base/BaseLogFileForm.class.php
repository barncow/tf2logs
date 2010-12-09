<?php

/**
 * LogFile form base class.
 *
 * @method LogFile getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogFileForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'log_id'   => new sfWidgetFormInputHidden(),
      'log_data' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'log_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('log_id')), 'empty_value' => $this->getObject()->get('log_id'), 'required' => false)),
      'log_data' => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('log_file[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogFile';
  }

}
