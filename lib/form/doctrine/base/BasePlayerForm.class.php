<?php

/**
 * Player form base class.
 *
 * @method Player getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlayerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'numeric_steamid' => new sfWidgetFormInputText(),
      'steamid'         => new sfWidgetFormInputText(),
      'credential'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'numeric_steamid' => new sfValidatorInteger(),
      'steamid'         => new sfValidatorString(array('max_length' => 30)),
      'credential'      => new sfValidatorString(array('max_length' => 10, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('player[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Player';
  }

}
