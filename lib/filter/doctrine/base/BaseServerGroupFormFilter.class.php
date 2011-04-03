<?php

/**
 * ServerGroup filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServerGroupFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'slug'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'owner_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Owner'), 'add_empty' => true)),
      'group_type'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'slug'            => new sfValidatorPass(array('required' => false)),
      'name'            => new sfValidatorPass(array('required' => false)),
      'owner_player_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Owner'), 'column' => 'id')),
      'group_type'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('server_group_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServerGroup';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'slug'            => 'Text',
      'name'            => 'Text',
      'owner_player_id' => 'ForeignKey',
      'group_type'      => 'Text',
    );
  }
}
