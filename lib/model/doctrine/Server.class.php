<?php

/**
 * Server
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Brian Barnekow
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Server extends BaseServer {
  const STATUS_NOT_VERIFIED = 'N';
  const STATUS_INACTIVE = 'I'; //previous owner gave up rights to this server

  public function saveNewServer($slug, $name, $ip, $port, $owner_id) {
    $serverGroup = new ServerGroup();
    $serverGroup->setSlug($slug);
    $serverGroup->setName($name);
    $serverGroup->setOwnerId($owner_id);
    
    $this->ip = $ip;
    $this->port = $port;
    $this->ServerGroup = $serverGroup;
    
    $this->verify_key = $this->generateVerifyKey($name);
    $this->status = self::STATUS_NOT_VERIFIED;
    
    $this->save();
  }
  
  public function generateVerifyKey($serverName = null) {
    if(!$serverName) {
      $serverName = $this->name;
    }
    $key = "tf2logs:";
    
    $fieldlength = $this->getTable()->getColumnDefinition('verify_key');
    $fieldlength = $fieldlength['length'];
    
    $keylength = $fieldlength-strlen($key);
    return $key.substr(sha1($serverName.time()), 0 ,$keylength);
  }
  
  /**
    Since single servers inherit settings from the group, need to modify these getters to 
    look at the group if needed.
  */
  public function getSlug() {
    if(!$this->_get('slug')) {
      if(!$this->_get('ServerGroup')) {
        return null;
      } else {
        return $this->ServerGroup->getSlug();
      }
    } else {
      return $this->_get('slug');
    }
  }
  
  public function getName() {
    if(!$this->_get('name')) {
      if(!$this->_get('ServerGroup')) {
        return null;
      } else {
        return $this->ServerGroup->getName();
      }
    } else {
      return $this->_get('name');
    }
  }
}
