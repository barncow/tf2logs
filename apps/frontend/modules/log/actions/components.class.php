<?php
 
class logComponents extends sfComponents {
  public function executeShowLog() {
    $id = $this->log['id'];
    $this->weapons = Doctrine::getTable('Weapon')->getMiniWeaponsForLogId($id);
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getWeaponStatsForLogId($id);  
    $this->playerStats = Doctrine::getTable('PlayerStat')->getPlayerStatsForLogId($id);
    $this->playerHealStats = Doctrine::getTable('PlayerHealStat')->getPlayerHealStatsForLogId($id);
    $this->itemPickupStats = Doctrine::getTable('ItemPickupStat')->getItemPickupStatsForLogId($id);
    $this->chatEvents = Doctrine::getTable('Event')->getChatEventsByIdAsArray($id);
  }
  
  public function executeIndexInfoBoxes() {
    $this->recentlyAdded = Doctrine::getTable('Log')->getMostRecentLogs();
    $this->topViewedLogs = Doctrine::getTable('Log')->getTopViewedLogs();
    $this->topuploaders = Doctrine::getTable('Player')->getTopUploaders();
  }
}
