<?php
 
class logComponents extends sfComponents {
  public function executeShowLog() {
    $id = $this->log['id'];
    $this->weapons = Doctrine::getTable('Weapon')->getMiniWeaponsForLogId($id);
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getWeaponStatsForLogId($id);  
    $this->playerStats = Doctrine::getTable('PlayerStat')->getPlayerStatsForLogId($id);
  }
}
