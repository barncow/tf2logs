<?php

/**
 * BaseUsedRole
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $role_id
 * @property integer $stat_id
 * @property Role $Role
 * @property Stat $Stat
 * 
 * @method integer  getRoleId()  Returns the current record's "role_id" value
 * @method integer  getStatId()  Returns the current record's "stat_id" value
 * @method Role     getRole()    Returns the current record's "Role" value
 * @method Stat     getStat()    Returns the current record's "Stat" value
 * @method UsedRole setRoleId()  Sets the current record's "role_id" value
 * @method UsedRole setStatId()  Sets the current record's "stat_id" value
 * @method UsedRole setRole()    Sets the current record's "Role" value
 * @method UsedRole setStat()    Sets the current record's "Stat" value
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseUsedRole extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('used_role');
        $this->hasColumn('role_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('stat_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Role', array(
             'local' => 'role_id',
             'foreign' => 'id'));

        $this->hasOne('Stat', array(
             'local' => 'stat_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}