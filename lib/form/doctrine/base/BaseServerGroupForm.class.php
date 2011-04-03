<?php

/**
 * ServerGroup form base class.
 *
 * @method ServerGroup getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServerGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'slug'            => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'owner_player_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Owner'), 'add_empty' => false)),
      'group_type'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'slug'            => new sfValidatorString(array('max_length' => 30)),
      'name'            => new sfValidatorString(array('max_length' => 100)),
      'owner_player_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Owner'))),
      'group_type'      => new sfValidatorString(array('max_length' => 1)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ServerGroup', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('server_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServerGroup';
  }

}
