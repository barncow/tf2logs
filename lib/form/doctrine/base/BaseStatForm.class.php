<?php

/**
 * Stat form base class.
 *
 * @method Stat getObject() Returns the current form's model object
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'log_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => false)),
      'name'                    => new sfWidgetFormInputText(),
      'player_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Player'), 'add_empty' => false)),
      'team'                    => new sfWidgetFormInputText(),
      'kills'                   => new sfWidgetFormInputText(),
      'assists'                 => new sfWidgetFormInputText(),
      'deaths'                  => new sfWidgetFormInputText(),
      'damage'                  => new sfWidgetFormInputText(),
      'longest_kill_streak'     => new sfWidgetFormInputText(),
      'capture_points_blocked'  => new sfWidgetFormInputText(),
      'capture_points_captured' => new sfWidgetFormInputText(),
      'dominations'             => new sfWidgetFormInputText(),
      'times_dominated'         => new sfWidgetFormInputText(),
      'revenges'                => new sfWidgetFormInputText(),
      'builtobjects'            => new sfWidgetFormInputText(),
      'destroyedobjects'        => new sfWidgetFormInputText(),
      'extinguishes'            => new sfWidgetFormInputText(),
      'ubers'                   => new sfWidgetFormInputText(),
      'dropped_ubers'           => new sfWidgetFormInputText(),
      'healing'                 => new sfWidgetFormInputText(),
      'weapons_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Weapon')),
      'roles_list'              => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Role')),
      'players_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Player')),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'log_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Log'))),
      'name'                    => new sfValidatorString(array('max_length' => 100)),
      'player_id'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Player'))),
      'team'                    => new sfValidatorString(array('max_length' => 4)),
      'kills'                   => new sfValidatorInteger(array('required' => false)),
      'assists'                 => new sfValidatorInteger(array('required' => false)),
      'deaths'                  => new sfValidatorInteger(array('required' => false)),
      'damage'                  => new sfValidatorInteger(array('required' => false)),
      'longest_kill_streak'     => new sfValidatorInteger(array('required' => false)),
      'capture_points_blocked'  => new sfValidatorInteger(array('required' => false)),
      'capture_points_captured' => new sfValidatorInteger(array('required' => false)),
      'dominations'             => new sfValidatorInteger(array('required' => false)),
      'times_dominated'         => new sfValidatorInteger(array('required' => false)),
      'revenges'                => new sfValidatorInteger(array('required' => false)),
      'builtobjects'            => new sfValidatorInteger(array('required' => false)),
      'destroyedobjects'        => new sfValidatorInteger(array('required' => false)),
      'extinguishes'            => new sfValidatorInteger(array('required' => false)),
      'ubers'                   => new sfValidatorInteger(array('required' => false)),
      'dropped_ubers'           => new sfValidatorInteger(array('required' => false)),
      'healing'                 => new sfValidatorInteger(array('required' => false)),
      'weapons_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Weapon', 'required' => false)),
      'roles_list'              => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Role', 'required' => false)),
      'players_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Player', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('stat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Stat';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['weapons_list']))
    {
      $this->setDefault('weapons_list', $this->object->Weapons->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['roles_list']))
    {
      $this->setDefault('roles_list', $this->object->Roles->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['players_list']))
    {
      $this->setDefault('players_list', $this->object->Players->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveWeaponsList($con);
    $this->saveRolesList($con);
    $this->savePlayersList($con);

    parent::doSave($con);
  }

  public function saveWeaponsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['weapons_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Weapons->getPrimaryKeys();
    $values = $this->getValue('weapons_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Weapons', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Weapons', array_values($link));
    }
  }

  public function saveRolesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['roles_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Roles->getPrimaryKeys();
    $values = $this->getValue('roles_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Roles', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Roles', array_values($link));
    }
  }

  public function savePlayersList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['players_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Players->getPrimaryKeys();
    $values = $this->getValue('players_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Players', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Players', array_values($link));
    }
  }

}
