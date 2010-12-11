<?php

/**
 * Stat filter form base class.
 *
 * @package    tf2logs
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStatFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'log_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Log'), 'add_empty' => true)),
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'player_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Player'), 'add_empty' => true)),
      'team'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'kills'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'assists'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'deaths'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'longest_kill_streak'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'capture_points_blocked'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'capture_points_captured' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'dominations'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'times_dominated'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'revenges'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'builtobjects'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'destroyedobjects'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'extinguishes'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ubers'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'dropped_ubers'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'weapons_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Weapon')),
      'roles_list'              => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Role')),
    ));

    $this->setValidators(array(
      'log_id'                  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Log'), 'column' => 'id')),
      'name'                    => new sfValidatorPass(array('required' => false)),
      'player_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Player'), 'column' => 'id')),
      'team'                    => new sfValidatorPass(array('required' => false)),
      'kills'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'assists'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'deaths'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'longest_kill_streak'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'capture_points_blocked'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'capture_points_captured' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'dominations'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'times_dominated'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'revenges'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'builtobjects'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'destroyedobjects'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'extinguishes'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ubers'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'dropped_ubers'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'weapons_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Weapon', 'required' => false)),
      'roles_list'              => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Role', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('stat_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addWeaponsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.UsedWeapon UsedWeapon')
      ->andWhereIn('UsedWeapon.weapon_id', $values)
    ;
  }

  public function addRolesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.UsedRole UsedRole')
      ->andWhereIn('UsedRole.role_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Stat';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'log_id'                  => 'ForeignKey',
      'name'                    => 'Text',
      'player_id'               => 'ForeignKey',
      'team'                    => 'Text',
      'kills'                   => 'Number',
      'assists'                 => 'Number',
      'deaths'                  => 'Number',
      'longest_kill_streak'     => 'Number',
      'capture_points_blocked'  => 'Number',
      'capture_points_captured' => 'Number',
      'dominations'             => 'Number',
      'times_dominated'         => 'Number',
      'revenges'                => 'Number',
      'builtobjects'            => 'Number',
      'destroyedobjects'        => 'Number',
      'extinguishes'            => 'Number',
      'ubers'                   => 'Number',
      'dropped_ubers'           => 'Number',
      'weapons_list'            => 'ManyKey',
      'roles_list'              => 'ManyKey',
    );
  }
}
