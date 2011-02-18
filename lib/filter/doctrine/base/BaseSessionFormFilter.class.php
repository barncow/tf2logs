<?php

/**
 * Session filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSessionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'sdata' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'stime' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'sdata' => new sfValidatorPass(array('required' => false)),
      'stime' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('session_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Session';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Text',
      'sdata' => 'Text',
      'stime' => 'Number',
    );
  }
}
