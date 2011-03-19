<?php

/**
 * ItemPickupStat filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseItemPickupStatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'item_key_name'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'times_picked_up' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'item_key_name'   => new sfValidatorPass(array('required' => false)),
      'times_picked_up' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('item_pickup_stat_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemPickupStat';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'stat_id'         => 'Number',
      'item_key_name'   => 'Text',
      'times_picked_up' => 'Number',
    );
  }
}
