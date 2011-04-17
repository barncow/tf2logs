<?php

class myUser extends sfBasicSecurityUser {
  public function getCurrentPlayerId() {
    return $this->getAttribute(sfConfig::get('app_playerid_session_var'));
  }
  
  public function isOwner() {
    return $this->isAuthenticated() && $this->hasCredential('owner');
  }
  
  public function doesUserOwn($owner_id) {
    return $this->isAuthenticated() && ($this->getCurrentPlayerId() == $owner_id || $this->hasCredential('owner'));
  }
}
