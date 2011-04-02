<?php

/**
 * Server filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'slug'            => new sfWidgetFormFilterInput(),
      'name'            => new sfWidgetFormFilterInput(),
      'ip'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'port'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'server_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ServerGroup'), 'add_empty' => true)),
      'last_message'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'verify_key'      => new sfWidgetFormFilterInput(),
      'status'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'slug'            => new sfValidatorPass(array('required' => false)),
      'name'            => new sfValidatorPass(array('required' => false)),
      'ip'              => new sfValidatorPass(array('required' => false)),
      'port'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'server_group_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ServerGroup'), 'column' => 'id')),
      'last_message'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'verify_key'      => new sfValidatorPass(array('required' => false)),
      'status'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('server_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Server';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'slug'            => 'Text',
      'name'            => 'Text',
      'ip'              => 'Text',
      'port'            => 'Number',
      'server_group_id' => 'ForeignKey',
      'last_message'    => 'Date',
      'verify_key'      => 'Text',
      'status'          => 'Text',
    );
  }
}
