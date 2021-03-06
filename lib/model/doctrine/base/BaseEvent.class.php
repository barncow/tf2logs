<?php

/**
 * BaseEvent
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $log_id
 * @property string $event_type
 * @property integer $elapsed_seconds
 * @property integer $attacker_player_id
 * @property string $attacker_coord
 * @property integer $victim_player_id
 * @property string $victim_coord
 * @property integer $assist_player_id
 * @property string $assist_coord
 * @property integer $chat_player_id
 * @property string $text
 * @property string $team
 * @property string $capture_point
 * @property integer $blue_score
 * @property integer $red_score
 * @property integer $weapon_id
 * @property Log $Log
 * @property Player $Attacker
 * @property Player $Victim
 * @property Player $Assist
 * @property Player $Chat
 * @property Weapon $Weapon
 * @property Doctrine_Collection $EventPlayers
 * 
 * @method integer             getLogId()              Returns the current record's "log_id" value
 * @method string              getEventType()          Returns the current record's "event_type" value
 * @method integer             getElapsedSeconds()     Returns the current record's "elapsed_seconds" value
 * @method integer             getAttackerPlayerId()   Returns the current record's "attacker_player_id" value
 * @method string              getAttackerCoord()      Returns the current record's "attacker_coord" value
 * @method integer             getVictimPlayerId()     Returns the current record's "victim_player_id" value
 * @method string              getVictimCoord()        Returns the current record's "victim_coord" value
 * @method integer             getAssistPlayerId()     Returns the current record's "assist_player_id" value
 * @method string              getAssistCoord()        Returns the current record's "assist_coord" value
 * @method integer             getChatPlayerId()       Returns the current record's "chat_player_id" value
 * @method string              getText()               Returns the current record's "text" value
 * @method string              getTeam()               Returns the current record's "team" value
 * @method string              getCapturePoint()       Returns the current record's "capture_point" value
 * @method integer             getBlueScore()          Returns the current record's "blue_score" value
 * @method integer             getRedScore()           Returns the current record's "red_score" value
 * @method integer             getWeaponId()           Returns the current record's "weapon_id" value
 * @method Log                 getLog()                Returns the current record's "Log" value
 * @method Player              getAttacker()           Returns the current record's "Attacker" value
 * @method Player              getVictim()             Returns the current record's "Victim" value
 * @method Player              getAssist()             Returns the current record's "Assist" value
 * @method Player              getChat()               Returns the current record's "Chat" value
 * @method Weapon              getWeapon()             Returns the current record's "Weapon" value
 * @method Doctrine_Collection getEventPlayers()       Returns the current record's "EventPlayers" collection
 * @method Event               setLogId()              Sets the current record's "log_id" value
 * @method Event               setEventType()          Sets the current record's "event_type" value
 * @method Event               setElapsedSeconds()     Sets the current record's "elapsed_seconds" value
 * @method Event               setAttackerPlayerId()   Sets the current record's "attacker_player_id" value
 * @method Event               setAttackerCoord()      Sets the current record's "attacker_coord" value
 * @method Event               setVictimPlayerId()     Sets the current record's "victim_player_id" value
 * @method Event               setVictimCoord()        Sets the current record's "victim_coord" value
 * @method Event               setAssistPlayerId()     Sets the current record's "assist_player_id" value
 * @method Event               setAssistCoord()        Sets the current record's "assist_coord" value
 * @method Event               setChatPlayerId()       Sets the current record's "chat_player_id" value
 * @method Event               setText()               Sets the current record's "text" value
 * @method Event               setTeam()               Sets the current record's "team" value
 * @method Event               setCapturePoint()       Sets the current record's "capture_point" value
 * @method Event               setBlueScore()          Sets the current record's "blue_score" value
 * @method Event               setRedScore()           Sets the current record's "red_score" value
 * @method Event               setWeaponId()           Sets the current record's "weapon_id" value
 * @method Event               setLog()                Sets the current record's "Log" value
 * @method Event               setAttacker()           Sets the current record's "Attacker" value
 * @method Event               setVictim()             Sets the current record's "Victim" value
 * @method Event               setAssist()             Sets the current record's "Assist" value
 * @method Event               setChat()               Sets the current record's "Chat" value
 * @method Event               setWeapon()             Sets the current record's "Weapon" value
 * @method Event               setEventPlayers()       Sets the current record's "EventPlayers" collection
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Brian Barnekow
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEvent extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('event');
        $this->hasColumn('log_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('event_type', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 10,
             ));
        $this->hasColumn('elapsed_seconds', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('attacker_player_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('attacker_coord', 'string', 17, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 17,
             ));
        $this->hasColumn('victim_player_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('victim_coord', 'string', 17, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 17,
             ));
        $this->hasColumn('assist_player_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('assist_coord', 'string', 17, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 17,
             ));
        $this->hasColumn('chat_player_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('text', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('team', 'string', 4, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 4,
             ));
        $this->hasColumn('capture_point', 'string', 30, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 30,
             ));
        $this->hasColumn('blue_score', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 2,
             ));
        $this->hasColumn('red_score', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 2,
             ));
        $this->hasColumn('weapon_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Log', array(
             'local' => 'log_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Player as Attacker', array(
             'local' => 'attacker_player_id',
             'foreign' => 'id'));

        $this->hasOne('Player as Victim', array(
             'local' => 'victim_player_id',
             'foreign' => 'id'));

        $this->hasOne('Player as Assist', array(
             'local' => 'assist_player_id',
             'foreign' => 'id'));

        $this->hasOne('Player as Chat', array(
             'local' => 'chat_player_id',
             'foreign' => 'id'));

        $this->hasOne('Weapon', array(
             'local' => 'weapon_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('EventPlayer as EventPlayers', array(
             'local' => 'id',
             'foreign' => 'event_id'));
    }
}