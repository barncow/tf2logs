<?php

/**
 * WeaponTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class WeaponTable extends Doctrine_Table {
    /**
     * Returns an instance of this class.
     *
     * @return object WeaponTable
     */
    public static function getInstance() {
        return Doctrine_Core::getTable('Weapon');
    }
    
    public function getAllWeapons() {
      return $this
        ->createQuery('w')
        ->leftJoin('w.Role r')
        ->orderBy('w.name asc')
        ->execute();
    }
    
    /**
    * Gets unique weapons that were used in the log
    */
    public function getWeaponsForLogId($logid) {
      return $this
        ->createQuery('w')
        ->innerJoin('w.WeaponStat ws')
        ->innerJoin('ws.Stat s')
        ->innerJoin('s.Log l')
        ->where('l.id = ?', $logid)
        ->orderBy('w.name')
        ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->execute();
    }
}