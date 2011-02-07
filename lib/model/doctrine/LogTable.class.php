<?php

/**
 * LogTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class LogTable extends Doctrine_Table {
    /**
     * Returns an instance of this class.
     *
     * @return object LogTable
     */
    public static function getInstance() {
        return Doctrine_Core::getTable('Log');
    }
    
    public function getLogById($id) {
      $l = $this
        ->createQuery('l')
        ->where('l.id = ?', $id)
        ->leftJoin('l.Stats s')
        ->leftJoin('s.Player p')
        ->leftJoin('s.Weapons w')
        ->leftJoin('s.RoleStats rs')
        ->leftJoin('rs.Role r')
        ->andWhere('l.error_log_name is null')
        ->orderBy('s.team asc, s.name asc, rs.time_played desc')
        ->execute();
     
      if(count($l) == 0) return null;
      return $l[0]; //returns doctrine_collection obj, we only want first (all we should get)
    }
    
    public function getLogByIdAsArray($id) {
      $l = $this
        ->createQuery('l')
        ->where('l.id = ?', $id)
        ->leftJoin('l.Submitter sr')
        ->leftJoin('l.Stats s')
        ->leftJoin('s.Player p')
        ->leftJoin('s.Weapons w')
        ->leftJoin('s.RoleStats rs')
        ->leftJoin('rs.Role r')
        ->andWhere('l.error_log_name is null')
        ->orderBy('s.team asc, s.name asc, rs.time_played desc')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->execute();
     
      if(count($l) == 0) return null;
      return $l[0]; //returns doctrine_collection obj, we only want first (all we should get)
    }
    
    public function getErrorLogById($id) {
      $l = $this
        ->createQuery('l')
        ->where('l.id = ?', $id)
        ->andWhere('l.error_log_name is not null')
        ->execute();
     
      if(count($l) == 0) return null;
      return $l[0]; //returns doctrine_collection obj, we only want first (all we should get)
    }
    
    public function listErrorLogs() {
      return $this
        ->createQuery('l')
        ->where('l.error_log_name is not null')
        ->orderBy('l.created_at ASC')
        ->execute();
    }
    
    public function deleteLog($log_id) {
      $this->createQuery('l')
        ->delete('Log l')
        ->where('l.id = ?', $log_id)
        ->execute();
    }
    
    public function getMostRecentLogs($num_to_retrieve = 10) {
      return $this
        ->createQuery('l')
        ->where('l.error_log_name is null')
        ->orderBy('l.created_at DESC')
        ->limit($num_to_retrieve)
        ->execute();
    }
    
    //retrieves logs that the user participated in, not submitted
    public function getParticipantLogsByPlayerNumericSteamidQuery($playerId) {
      return $this
        ->createQuery('l')
        ->leftJoin('l.Stats s')
        ->leftJoin('s.Player p')
        ->where('p.numeric_steamid = ?', $playerId)
        ->orderBy('l.created_at desc')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);
    }
    
    //retrieves logs that the user submitted in, not necessarily just played in
    public function getSubmittedLogsByPlayerNumericSteamidQuery($playerId) {
      return $this
        ->createQuery('l')
        ->leftJoin('l.Submitter p')
        ->where('p.numeric_steamid = ?', $playerId)
        ->orderBy('l.created_at desc')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);
    }
    
    public function getNumberSubmittedLogsByPlayerNumericSteamid($playerId) {
      $p = Doctrine_Query::create()
      ->select('count(l.id) as num_logs')
      ->from('Log l')
      ->leftJoin('l.Submitter p')
      ->where('p.numeric_steamid = ?', $playerId)
      ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
      ->execute();
      if(count($p) != 1) return 0;
      return $p[0]['num_logs'];
    }
    
    //retrieves an array of distinct maps
    //first variable allows you to put in an array to prepopulate (for instance, a blank Pick One value)
    //you can optionally put in a seedMaps variable.
    //This allows you to return more maps (unique and sorted)
    //than there currently are in the DB.
    //$seedMaps should follow same structure as return value.
    public function getMapsAsList(&$ret = array(), $seedMaps = null) {
      $m = Doctrine_Query::create()
      ->select('l.map_name as map_name')
      ->from('Log l')
      ->where('l.map_name is not null')
      ->distinct(true)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
      ->orderBy('l.map_name asc')
      ->execute();
      if(!is_array($m)){
        $ret[$m] = $m;
      } else {
        foreach($m as $map) {
          $ret[$map] = $map;
        }
      }
      if($seedMaps != null && count($seedMaps)) {
        $ret = array_merge($ret, $seedMaps);
        sort($ret);
      }
      return $ret;
    }
    
    //retrieves logs based on search criteria
    public function getLogsFromSearch($logName, $mapName, $fromDate, $toDate) {
      $q = $this
        ->createQuery('l')
        ->orderBy('l.created_at desc, l.name asc')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);
      if($logName && strlen($logName) > 0) {
        $q->andWhere('l.name LIKE ?', '%'.$logName.'%');
      }
      
      if($mapName && strlen($mapName) > 0) {
        $q->andWhere('l.map_name = ?', $mapName);
      }
      
      if($fromDate && strlen($fromDate) > 0) {
        $q->andWhere('l.created_at >= ?', $fromDate);
      }
      
      if($toDate && strlen($toDate) > 0) {
        //add one to the date in order to compare all dates and times before this date.
        $toDate = date("Y-m-d", strtotime($toDate." +1 day"));
        $q->andWhere('l.created_at < ?', $toDate);
      }
        
      return $q;
    }
}
