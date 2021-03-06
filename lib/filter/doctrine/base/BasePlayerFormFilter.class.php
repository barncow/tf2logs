<?php

/**
 * Player filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
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
      'last_login'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'views'           => new sfWidgetFormFilterInput(),
      'avatar_url'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'numeric_steamid' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'steamid'         => new sfValidatorPass(array('required' => false)),
      'credential'      => new sfValidatorPass(array('required' => false)),
      'name'            => new sfValidatorPass(array('required' => false)),
      'last_login'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'views'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'avatar_url'      => new sfValidatorPass(array('required' => false)),
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
      'last_login'      => 'Date',
      'views'           => 'Number',
      'avatar_url'      => 'Text',
    );
  }
}
