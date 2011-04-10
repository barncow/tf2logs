<?php

/**
 * ItemPickupStat form base class.
 *
 * @method ItemPickupStat getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseItemPickupStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'stat_id'         => new sfWidgetFormInputHidden(),
      'item_key_name'   => new sfWidgetFormInputHidden(),
      'times_picked_up' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'stat_id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('stat_id')), 'empty_value' => $this->getObject()->get('stat_id'), 'required' => false)),
      'item_key_name'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('item_key_name')), 'empty_value' => $this->getObject()->get('item_key_name'), 'required' => false)),
      'times_picked_up' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('item_pickup_stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemPickupStat';
  }

}
