<?php

/**
 * PlayerStatTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PlayerStatTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object PlayerStatTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PlayerStat');
    }
    
    /**
    * Gets the data that was recorded for the log for use in the players kills,deaths
    */
    public function getPlayerStatsForLogId($logid) {
      return Doctrine_Query::create()
        ->select('ps.stat_id, ps.player_id, sum(ps.kills) as num_kills, sum(ps.deaths) as num_deaths')
        ->from('PlayerStat ps')
        ->innerJoin('ps.Stat s')
        ->innerJoin('s.Log l')
        ->innerJoin('ps.Player p')
        ->where('l.id = ?', $logid)
        ->groupBy('s.name, p.id')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->execute();
    }
}