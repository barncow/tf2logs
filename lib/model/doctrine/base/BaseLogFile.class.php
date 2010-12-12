<?php

/**
 * BaseLogFile
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $log_id
 * @property clob $log_data
 * @property Log $Log
 * 
 * @method integer getLogId()    Returns the current record's "log_id" value
 * @method clob    getLogData()  Returns the current record's "log_data" value
 * @method Log     getLog()      Returns the current record's "Log" value
 * @method LogFile setLogId()    Sets the current record's "log_id" value
 * @method LogFile setLogData()  Sets the current record's "log_data" value
 * @method LogFile setLog()      Sets the current record's "Log" value
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLogFile extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('log_file');
        $this->hasColumn('log_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('log_data', 'clob', 16777215, array(
             'type' => 'clob',
             'notnull' => true,
             'length' => 16777215,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Log', array(
             'local' => 'log_id',
             'foreign' => 'id'));
    }
}