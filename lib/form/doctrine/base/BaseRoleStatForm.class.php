<?php

/**
 * RoleStat form base class.
 *
 * @method RoleStat getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRoleStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'role_id'     => new sfWidgetFormInputHidden(),
      'stat_id'     => new sfWidgetFormInputHidden(),
      'time_played' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'role_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('role_id')), 'empty_value' => $this->getObject()->get('role_id'), 'required' => false)),
      'stat_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('stat_id')), 'empty_value' => $this->getObject()->get('stat_id'), 'required' => false)),
      'time_played' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('role_stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RoleStat';
  }

}
