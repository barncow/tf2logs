<?php

/**
 * PlayerHealStat filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlayerHealStatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'healing'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'healing'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('player_heal_stat_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlayerHealStat';
  }

  public function getFields()
  {
    return array(
      'player_id' => 'Number',
      'stat_id'   => 'Number',
      'healing'   => 'Number',
    );
  }
}
