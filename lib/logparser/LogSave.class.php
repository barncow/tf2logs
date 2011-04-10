<?php

class LogSave {
  protected $conn;
  protected $log;
  
  /**
    If lineByLine is true, then the saved log object is returned. This is only necessary for repeated saves of the log.
    Only useful for the udpserver.
  */
  public function save($log, $lineByLine = false) {
    $this->log = $log;
    $this->conn = Doctrine_Manager::connection();
    $ret = null;
    try {
        $this->conn->beginTransaction();
        $ret = $this->doWork($lineByLine);
        $this->conn->commit();
    } catch(Doctrine_Exception $e) {
        $this->conn->rollback();
        throw $e;
    }
    return $ret;
  }
  
  protected function doWork($lineByLine) {
    $this->log->save();
    $logid = $this->log->getId();
    
    $this->saveEvents($this->log->getEventsArray(true), $logid);
    $this->saveStatChildren($logid, $lineByLine);
    
    if($lineByLine) return $this->log;
    else return $logid;
  }
  
  protected function saveStatChildren($logid, $lineByLine) {
    foreach($this->log->Stats as $stat) {
      $statid = $stat->getId();
      
      $this->saveStatsTable('WeaponStat', $stat->getWeaponStatsArray(true), $statid, $lineByLine, 'weapon_id');
      $this->saveStatsTable('PlayerStat', $stat->getPlayerStatsArray(true), $statid, $lineByLine, 'player_id');
      $this->saveStatsTable('PlayerHealStat', $stat->getPlayerHealStatsArray(true), $statid, $lineByLine, 'player_id');
      $this->saveStatsTable('ItemPickupStat', $stat->getItemPickupStatsArray(true), $statid, $lineByLine, 'item_key_name');
      $this->saveStatsTable('RoleStat', $stat->getRoleStatsArray(true), $statid, $lineByLine, 'role_id');
    }
  }
  
  /**
    This is a convenience function to save the Stat Table's children, such as WeaponStats
    $extraIdKeyName is the key name of the field opposite of the stat_id field for the composite PK.
  */
  protected function saveStatsTable($tableName, $statsTableArray, $statId, $lineByLine, $extraIdKeyName = null) {
    if(!$statsTableArray || count($statsTableArray) == 0) return; //nothing to do
    $table = Doctrine::getTable($tableName);
    foreach($statsTableArray as $obj) {
      $obj['stat_id'] = $statId;
      
      $affrows = null;
      if($lineByLine) {
        $affrows = $this->updateStatTable($table, $obj, array('stat_id' => $obj['stat_id'], $extraIdKeyName => $obj[$extraIdKeyName]));
      } 
      
      if(!$lineByLine || !$affrows) {
        //optimization - if not doing line by line, the always inserting. Otherwise, insert only if affected rows from update is falsy.
        $this->conn->insert($table, $obj);
      }
    }
  }
  
  /**
   * Update the stat table by incrementing the rows with the given fields. "Borrowed" from Doctrine, to be able to increment instead of just setting a value.
   *
   * @throws Doctrine_Connection_Exception    if something went wrong at the database level
   * @param Doctrine_Table $table     The table to insert data into
   * @param array $values             An associative array containing column-value pairs.
   *                                  Values can be strings or Doctrine_Expression instances.
   * @return integer                  the number of affected rows. Boolean false if empty value array was given,
   */
  protected function updateStatTable(Doctrine_Table $table, array $fields, array $identifier) {
    if (empty($fields)) {
      return false;
    }
    
    //remove elements from fields (which has our update values) which are in identifier, otherwise keys will be incremented.
    foreach(array_keys($identifier) as $fieldName) {
      if(isset($fields[$fieldName])) unset($fields[$fieldName]);
    }

    //gather our fields as a set statement within an update.
    $set = array();
    foreach ($fields as $fieldName => $value) {
      $set[] = $this->conn->quoteIdentifier($table->getColumnName($fieldName)) . ' = '.$table->getColumnName($fieldName).'+?'; 
    }

    //create update sql
    $sql  = 'UPDATE ' . $this->conn->quoteIdentifier($table->getTableName())
          . ' SET ' . implode(', ', $set)
          . ' WHERE ' . implode(' = ? AND ', $this->conn->quoteMultipleIdentifier(array_keys($identifier)))
          . ' = ?';
    
    //using base PDO statements here, could probably go back to using Doctrine Prepare.
    $stmt = $this->conn->getDbh()->prepare($sql);
    
    //merge our fields and identifier values in order so that they can replace the ?
    $params = array_merge(array_values($fields), array_values($identifier));
    
    $stmt->execute($params);
    return $stmt->rowCount();
  }
  
  protected function saveEvents($events, $logid) {
    if(!$events || count($events) <= 0) return;
    
    $eventTable = Doctrine::getTable('Event');
    $eventPlayerTable = Doctrine::getTable('EventPlayer');
    foreach($events as $e) {
      if(isset($e['updateLastKillEvent']) && $e['updateLastKillEvent']) {
        //we have a lone assist - find the last kill event and mark it as assist.

        $killevent = $eventTable->getLastKillEventForLogId($logid);
        
        unset($e['updateLastKillEvent']); //don't want it to try to update this as a field.
        unset($killevent['Log']); //because of the join, this will be here. Do not update this.
        
        $e = array_merge($killevent, $e);
        
        $this->conn->update($eventTable, $e, array('id' => $killevent['id']));
      } else {
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
}
