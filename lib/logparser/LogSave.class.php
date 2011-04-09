<?php

class LogSave {
  protected $conn;
  protected $log;
  
  /**
    If returnFullObj is true, then the saved log object is returned. This is only necessary for repeated saves of the log.
    Only useful for the udpserver.
  */
  public function save($log, $returnFullObj = false) {
    $this->log = $log;
    $this->conn = Doctrine_Manager::connection();
    $ret = null;
    try {
        $this->conn->beginTransaction();
        $ret = $this->doWork($returnFullObj);
        $this->conn->commit();
    } catch(Doctrine_Exception $e) {
        $this->conn->rollback();
        throw $e;
    }
    return $ret;
  }
  
  protected function doWork($returnFullObj) {
    $this->log->save();
    $logid = $this->log->getId();
    
    $this->saveEvents($this->log->getEventsArray(true), $logid);
    $this->saveStatChildren($logid);
    
    if($returnFullObj) return $this->log;
    else return $logid;
  }
  
  protected function saveStatChildren($logid) {
    foreach($this->log->Stats as $stat) {
      $statid = $stat->getId();
      $this->saveStatsTable('WeaponStat', $stat->getWeaponStatsArray(true), $statid);
      $this->saveStatsTable('PlayerStat', $stat->getPlayerStatsArray(true), $statid);
      $this->saveStatsTable('PlayerHealStat', $stat->getPlayerHealStatsArray(true), $statid);
      $this->saveStatsTable('ItemPickupStat', $stat->getItemPickupStatsArray(true), $statid);
      $this->saveStatsTable('RoleStat', $stat->getRoleStatsArray(true), $statid);
    }
  }
  
  /**
    This is a convenience function to save the Stat Table's children, such as WeaponStats
  */
  protected function saveStatsTable($tableName, $statsTableArray, $statId) {
    if(!$statsTableArray || count($statsTableArray) == 0) return; //nothing to do
    $table = Doctrine::getTable($tableName);
    foreach($statsTableArray as $obj) {
      $obj['stat_id'] = $statId;
      $this->conn->insert($table, $obj);
    }
  }
  
  protected function saveEvents($events, $logid) {
    if(!$events || count($events) <= 0) return;
    
    $eventTable = Doctrine::getTable('Event');
    $eventPlayerTable = Doctrine::getTable('EventPlayer');
    foreach($events as $e) {
      $e['log_id'] = $logid;
      $eps = null;
      if(isset($e['EventPlayer'])) {
        //if there are child records, we need to pop those off and save them later.
        $eps = $e['EventPlayer'];
        unset($e['EventPlayer']);
      }
      $this->conn->insert($eventTable, $e);
      
      if($eps && count($eps) > 0) {
        //save child records
        $eventid = $this->conn->lastInsertId('event');
        foreach($eps as $ep) {
          $ep['event_id'] = $eventid;
          $this->conn->insert($eventPlayerTable, $ep);
        }
      }
    }
  }
}
