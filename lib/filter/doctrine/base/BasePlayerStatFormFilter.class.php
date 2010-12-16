<?php

/**
 * PlayerStat filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlayerStatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'kills'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'deaths'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'kills'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'deaths'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('player_stat_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlayerStat';
  }

  public function getFields()
  {
    return array(
      'player_id' => 'Number',
      'stat_id'   => 'Number',
      'kills'     => 'Number',
      'deaths'    => 'Number',
    );
  }
}
