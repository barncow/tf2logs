<?php

/**
 * Player filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlayerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'numeric_steamid' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'steamid'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'credential'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'numeric_steamid' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'steamid'         => new sfValidatorPass(array('required' => false)),
      'credential'      => new sfValidatorPass(array('required' => false)),
      'name'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('player_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Player';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'numeric_steamid' => 'Number',
      'steamid'         => 'Text',
      'credential'      => 'Text',
      'name'            => 'Text',
    );
  }
}
