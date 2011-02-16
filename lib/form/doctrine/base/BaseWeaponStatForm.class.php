<?php

/**
 * WeaponStat form base class.
 *
 * @method WeaponStat getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseWeaponStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'weapon_id' => new sfWidgetFormInputHidden(),
      'stat_id'   => new sfWidgetFormInputHidden(),
      'kills'     => new sfWidgetFormInputText(),
      'deaths'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'weapon_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('weapon_id')), 'empty_value' => $this->getObject()->get('weapon_id'), 'required' => false)),
      'stat_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('stat_id')), 'empty_value' => $this->getObject()->get('stat_id'), 'required' => false)),
      'kills'     => new sfValidatorInteger(array('required' => false)),
      'deaths'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('weapon_stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'WeaponStat';
  }

}
