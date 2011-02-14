<?php

/**
 * BaseLog
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $redscore
 * @property integer $bluescore
 * @property integer $elapsed_time
 * @property string $map_name
 * @property integer $submitter_player_id
 * @property string $error_log_name
 * @property string $error_exception
 * @property integer $views
 * @property LogFile $LogFile
 * @property Player $Submitter
 * @property Doctrine_Collection $Stats
 * @property Doctrine_Collection $Events
 * 
 * @method integer             getId()                  Returns the current record's "id" value
 * @method string              getName()                Returns the current record's "name" value
 * @method integer             getRedscore()            Returns the current record's "redscore" value
 * @method integer             getBluescore()           Returns the current record's "bluescore" value
 * @method integer             getElapsedTime()         Returns the current record's "elapsed_time" value
 * @method string              getMapName()             Returns the current record's "map_name" value
 * @method integer             getSubmitterPlayerId()   Returns the current record's "submitter_player_id" value
 * @method string              getErrorLogName()        Returns the current record's "error_log_name" value
 * @method string              getErrorException()      Returns the current record's "error_exception" value
 * @method integer             getViews()               Returns the current record's "views" value
 * @method LogFile             getLogFile()             Returns the current record's "LogFile" value
 * @method Player              getSubmitter()           Returns the current record's "Submitter" value
 * @method Doctrine_Collection getStats()               Returns the current record's "Stats" collection
 * @method Doctrine_Collection getEvents()              Returns the current record's "Events" collection
 * @method Log                 setId()                  Sets the current record's "id" value
 * @method Log                 setName()                Sets the current record's "name" value
 * @method Log                 setRedscore()            Sets the current record's "redscore" value
 * @method Log                 setBluescore()           Sets the current record's "bluescore" value
 * @method Log                 setElapsedTime()         Sets the current record's "elapsed_time" value
 * @method Log                 setMapName()             Sets the current record's "map_name" value
 * @method Log                 setSubmitterPlayerId()   Sets the current record's "submitter_player_id" value
 * @method Log                 setErrorLogName()        Sets the current record's "error_log_name" value
 * @method Log                 setErrorException()      Sets the current record's "error_exception" value
 * @method Log                 setViews()               Sets the current record's "views" value
 * @method Log                 setLogFile()             Sets the current record's "LogFile" value
 * @method Log                 setSubmitter()           Sets the current record's "Submitter" value
 * @method Log                 setStats()               Sets the current record's "Stats" collection
 * @method Log                 setEvents()              Sets the current record's "Events" collection
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLog extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('log');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 100,
             ));
        $this->hasColumn('redscore', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('bluescore', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('elapsed_time', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('map_name', 'string', 25, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 25,
             ));
        $this->hasColumn('submitter_player_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('error_log_name', 'string', 50, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 50,
             ));
        $this->hasColumn('error_exception', 'string', 500, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 500,
             ));
        $this->hasColumn('views', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 4,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('LogFile', array(
             'local' => 'id',
             'foreign' => 'log_id'));

        $this->hasOne('Player as Submitter', array(
             'local' => 'submitter_player_id',
             'foreign' => 'id'));

        $this->hasMany('Stat as Stats', array(
             'local' => 'id',
             'foreign' => 'log_id'));

        $this->hasMany('Event as Events', array(
             'local' => 'id',
             'foreign' => 'log_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}