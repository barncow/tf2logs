<?php

/**
 * Stat
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Brian Barnekow
 */
class Stat extends BaseStat {  
  protected $currentLongestKillStreak = 0;
  
  /**
  * Sets the attributes found in the PlayerInfo object into this object.
  */
  function setPlayerInfoAttributes(PlayerInfo $playerInfo) {
    if(!isset($this->Player)) {
      $p = Doctrine::getTable('Player')->findOneBySteamid($playerInfo->getSteamid());
      if($p != null && $p !== false) {
        $this->setPlayer($p);
      } else {
        $p = new Player();
        $p->setSteamid($playerInfo->getSteamid());
        $p->save();
        $this->Player = $p;
      }
    }
    $this->setName($playerInfo->getName());
    $this->Player->setName($playerInfo->getName());
    $this->setTeam($playerInfo->getTeam());
  }
  
  /**
  * Convenience method to determine if this stat record is equal to
  * the given playerInfo.
  */
  public function equalsPlayerInfo(PlayerInfo $playerInfo) {
    return $this->getPlayer()->getSteamid() == $playerInfo->getSteamid();
  }
  
  /**
  * Gets the columns that can be incremented,
  * by filtering out certain values.
  */
  protected function getStatColumns() {
    return array_diff($this->getTable()->getColumnNames(), array('id', 'log_id', 'name', 'steamid', 'team'));
  }
  
  /**
  * Checks to see if the given statkey can be incremented.
  */
  protected function isKeyAbleToBeIncremented($statkey) {
    foreach($this->getStatColumns() as $col) {
      if($statkey == $col) return true;
    }
    return false;
  }
  
  /**
  * Increments the stat, if the key exists. Otherwise, it throws an exception.
  */
  public function incrementStat($statkey, $increment = 1) {
    if(!$this->isKeyAbleToBeIncremented($statkey)) {
      throw new InvalidArgumentException("Invalid key '$statkey' given to incrementStat method.");
    }
    
    $this->_set($statkey, $this->_get($statkey)+$increment);
    if($statkey == "kills") {
      ++$this->currentLongestKillStreak;
    } else if($statkey == "deaths") {
      if($this->currentLongestKillStreak > $this->getLongestKillStreak()) {
        $this->setLongestKillStreak($this->currentLongestKillStreak);
      }
      $this->currentLongestKillStreak = 0;
    }
  }
  
  /**
  * Calculates the stat's kills per death ratio.
  * @deprecated Calculation done in view since we are converting this to array, and this method will not be there.
  */
  public function getKillsPerDeath() {
    return $this->doPerDeathDivision($this->getKills());
  }
  
  /**
  * Calculates the stat's ubers per death ratio.
  * @deprecated Calculation done in view since we are converting this to array, and this method will not be there.
  */
  public function getUbersPerDeath() {
    return $this->doPerDeathDivision($this->getUbers());
  }
  
  /**
  * @deprecated Calculation done in view since we are converting this to array, and this method will not be there.
  */
  protected function doPerDeathDivision($numerator) {
    if($this->getDeaths() == 0) return $numerator;
    return round((float) $numerator/$this->getDeaths(), 3);
  }
  
  /**
  * This will add the given weapon to the player's stats.
  */
  public function incrementWeaponForPlayer($weapon, $propertyToIncrement, $increment = 1) {
    $addws = true;
    foreach($this->WeaponStats as &$ws) {
      if($ws->getWeaponId() == $weapon->getId()) {
        $ws->_set($propertyToIncrement, $ws->_get($propertyToIncrement)+$increment);
        $addws = false;
        break;
      }
    }
    if($addws) {
      $wsadd = new WeaponStat();
      $wsadd->setWeaponId($weapon->getId());
      $wsadd->setStat($this);
      $wsadd->_set($propertyToIncrement, $increment);
      $this->WeaponStats[] = $wsadd;
    }
  }
  
  /**
  * This will add the given player to this player's stats.
  */
  public function addPlayerStat($otherPlayer, $propertyToIncrement, $increment = 1) {
    $addps = true;
    foreach($this->PlayerStats as &$ps) {
      if($ps->getPlayerId() == $otherPlayer->getId()) {
        $ps->_set($propertyToIncrement, $ps->_get($propertyToIncrement)+$increment);
        $addps = false;
        break;
      }
    }
    if($addps) {
      $psadd = new PlayerStat();
      $psadd->setPlayerId($otherPlayer->getId());
      $psadd->setStat($this);
      $psadd->_set($propertyToIncrement, $increment);
      $this->PlayerStats[] = $psadd;
    }
  }
  
  /**
  * Used to perform any cleanup work.
  */
  public function finishStat($nowDt, $logStartDt) {
    //commit current role to the rolestat.
    $this->addUpdateRoleStat($nowDt, $logStartDt);
    
    //when a player disconnects, we need to finish them early.
    //If a player changes team, this should just start the player over after
    //saving their current class info.
    //todo should figure out what to do when a player switches teams, since A/D maps switch teams automatically
    $this->currentRole = null;
    $this->currentRoleSinceDt = null;
  }
  
  protected function addUpdateRoleStat($nowDt, $logStartDt) {
    if($this->currentRole == null || $this->currentRole->getId() == null) return;
    $addrs = true;
    $elapsedTime = $nowDt->getTimestamp()-$this->currentRoleSinceDt->getTimestamp();
    foreach($this->RoleStats as &$rs) {
      if($rs->getRoleId() == $this->currentRole->getId()) {
        $rs->_set('time_played', $rs->_get('time_played')+$elapsedTime);
        $addrs = false;
        break;
      }
    }
    if($addrs) {
      $rsadd = new RoleStat();
      $rsadd->setRoleId($this->currentRole->getId());
      $rsadd->setStat($this);
      $rsadd->_set('time_played', $elapsedTime);
      $this->RoleStats[] = $rsadd;
    }
  }
  
  //move to top when done
  protected $currentRole;
  protected $currentRoleSinceDt;
  
  /**
  * This will add the given role to the player's stats.
  * If the role does not exist in the database, an exception is thrown.
  */
  public function addRoleToPlayer($role, $nowDt, $logStartDt) {
    if($this->currentRole != null) {
      if($role->getId() == $this->currentRole->getId()) return; //same role, keep going.
      //different role coming in, and we did not just start. Add current
      //info to a playerstat
     $this->addUpdateRoleStat($nowDt, $logStartDt);
      
      $this->currentRoleSinceDt = $nowDt;
    } else {
      //current role is null, therefore we are just starting.
      //assume the user has been in the role since the start 
      //of the match.
      $this->currentRoleSinceDt = $logStartDt;
    }
    
    $this->currentRole = $role;
  }
}
