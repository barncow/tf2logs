<?php

/**
 * Log form base class.
 *
 * @method Log getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'name'                => new sfWidgetFormInputText(),
      'redscore'            => new sfWidgetFormInputText(),
      'bluescore'           => new sfWidgetFormInputText(),
      'elapsed_time'        => new sfWidgetFormInputText(),
      'game_seconds'        => new sfWidgetFormInputText(),
      'map_name'            => new sfWidgetFormInputText(),
      'submitter_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Submitter'), 'add_empty' => true)),
      'error_log_name'      => new sfWidgetFormInputText(),
      'error_exception'     => new sfWidgetFormTextarea(),
      'views'               => new sfWidgetFormInputText(),
      'is_auto'             => new sfWidgetFormInputCheckbox(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                => new sfValidatorString(array('max_length' => 100)),
      'redscore'            => new sfValidatorInteger(array('required' => false)),
      'bluescore'           => new sfValidatorInteger(array('required' => false)),
      'elapsed_time'        => new sfValidatorInteger(array('required' => false)),
      'game_seconds'        => new sfValidatorInteger(array('required' => false)),
      'map_name'            => new sfValidatorString(array('max_length' => 25, 'required' => false)),
      'submitter_player_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Submitter'), 'required' => false)),
      'error_log_name'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'error_exception'     => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'views'               => new sfValidatorInteger(array('required' => false)),
      'is_auto'             => new sfValidatorBoolean(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Log';
  }

}
