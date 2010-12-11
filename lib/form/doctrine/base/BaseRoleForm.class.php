<?php

/**
 * Role form base class.
 *
 * @method Role getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRoleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'key_name'   => new sfWidgetFormInputText(),
      'name'       => new sfWidgetFormInputText(),
      'stats_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Stat')),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'key_name'   => new sfValidatorString(array('max_length' => 12)),
      'name'       => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'stats_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Stat', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('role[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Role';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['stats_list']))
    {
      $this->setDefault('stats_list', $this->object->Stats->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveStatsList($con);

    parent::doSave($con);
  }

  public function saveStatsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['stats_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Stats->getPrimaryKeys();
    $values = $this->getValue('stats_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Stats', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Stats', array_values($link));
    }
  }

}
