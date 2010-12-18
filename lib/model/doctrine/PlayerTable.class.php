<?php

/**
 * PlayerTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PlayerTable extends Doctrine_Table {
  /**
   * Returns an instance of this class.
   *
   * @return object PlayerTable
   */
  public static function getInstance() {
      return Doctrine_Core::getTable('Player');
  }
    
  public function getPlayerStatsByNumericSteamid($id) {
    $p = Doctrine_Query::create()
      ->select('p.*'
        .', count(s.id) as num_matches'
        .', sum(s.kills) as kills'
        .', sum(s.assists) as assists'
        .', sum(s.deaths) as deaths'
        .', round(sum(s.kills)/sum(s.deaths), 3) as kills_per_death'
        .', sum(s.longest_kill_streak) as longest_kill_streak'
        .', sum(s.capture_points_blocked) as capture_points_blocked'
        .', sum(s.capture_points_captured) as capture_points_captured'
        .', sum(s.dominations) as dominations'
        .', sum(s.times_dominated) as times_dominated'
        .', sum(s.revenges) as revenges'
        .', sum(s.builtobjects) as built_objects'
        .', sum(s.destroyedobjects) as destroyed_objects'
        .', sum(s.extinguishes) as extinguishes'
        .', sum(s.ubers) as ubers'
        .', round(sum(s.ubers)/sum(s.deaths), 3) as ubers_per_death'
        .', sum(s.dropped_ubers) as dropped_ubers'
      )
      ->from('Player p')
      ->innerJoin('p.Stats s')
      ->where('p.numeric_steamid = ?', $id)
      ->groupBy('p.numeric_steamid')
      ->execute();
      
    if(count($p) == 0) return null;
    return $p[0]; //returns doctrine_collection obj, we only want first (all we should get)
  }
  
  public function getPlayerRolesByNumericSteamid($id) {
    $connection = Doctrine_Manager::connection();
    $query = 'SELECT p.id, r.name, COUNT( rs.role_id ) as num_times, sum(time_played) as time_played '
      .'FROM  role_stat rs '
      .'INNER JOIN stat s ON rs.stat_id = s.id '
      .'INNER JOIN player p ON p.id = s.player_id '
      .'INNER JOIN role r ON r.id = rs.role_id '
      .'WHERE p.numeric_steamid = ? '
      .'GROUP BY p.id, r.name '
      .'ORDER BY num_times DESC ';
    $statement = $connection->execute($query, array($id));
    $ret = array();
    while($row = $statement->fetch(PDO::FETCH_OBJ)) {
      $ret[] = $row;
    }
    return $ret;
  }
  
  public function getMostUsedPlayerName($id) {
    $connection = Doctrine_Manager::connection();
    $query = 'select s.name, count(s.id) num_times '
      .'from stat s '
      .'inner join player p on p.id = s.player_id '
      .'where p.numeric_steamid = ? '
      .'group by s.name '
      .'order by num_times desc '
      .'limit 0, 1 ';
    $statement = $connection->execute($query, array($id));
    $row = $statement->fetch(PDO::FETCH_OBJ);
    return $row->name;
  }
}
