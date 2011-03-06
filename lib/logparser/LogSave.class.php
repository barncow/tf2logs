<?php

class LogSave {
  protected $conn;
  protected $log;
  
  public function save($log) {
    $this->log = $log;
    $this->conn = Doctrine_Manager::connection();
    $id = null;
    try {
        $this->conn->beginTransaction();
        $id = $this->doWork();
        $this->conn->commit();
    } catch(Doctrine_Exception $e) {
        $this->conn->rollback();
        throw $e;
    }
    return $id;
  }
  
  protected function doWork() {
    $this->log->save();
    $logid = $this->log->getId();
    
    $this->saveEvents($this->log->getEvents(), $logid);
    
    return $logid;
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
