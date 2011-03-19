<?php

/**
 * PlayerHealStat form base class.
 *
 * @method PlayerHealStat getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlayerHealStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'player_id' => new sfWidgetFormInputHidden(),
      'stat_id'   => new sfWidgetFormInputHidden(),
      'healing'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'player_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('player_id')), 'empty_value' => $this->getObject()->get('player_id'), 'required' => false)),
      'stat_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('stat_id')), 'empty_value' => $this->getObject()->get('stat_id'), 'required' => false)),
      'healing'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('player_heal_stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlayerHealStat';
  }

}
