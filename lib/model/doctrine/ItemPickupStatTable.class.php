<?php

/**
 * ItemPickupStatTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ItemPickupStatTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object ItemPickupStatTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ItemPickupStat');
    }
    
    public function getItemPickupStatsForLogId($logid) {
      return Doctrine_Query::create()
        ->select('ips.stat_id, ips.item_key_name, sum(ips.times_picked_up) as times_picked_up')
        ->from('ItemPickupStat ips')
        ->innerJoin('ips.Stat s')
        ->innerJoin('s.Log l')
        ->where('l.id = ?', $logid)
        ->groupBy('s.name, ips.item_key_name')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->execute();
    }
    
    public function findArrayByStatId($statid) {
      return $this
        ->createQuery('ips')
        ->where('ips.stat_id = ?', $statid)
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->execute();
    }
}