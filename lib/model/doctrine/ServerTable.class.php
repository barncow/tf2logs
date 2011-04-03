<?php

/**
 * ServerTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ServerTable extends Doctrine_Table {
    /**
     * Returns an instance of this class.
     *
     * @return object ServerTable
     */
    public static function getInstance() {
        return Doctrine_Core::getTable('Server');
    }
    
    /**
      Checks if the given IP and port combination are being used by another active user. If it is, return true, else false.
    */
    public function isAddressUsed($ip, $port) {
      if(!$ip || !$port) throw new IllegalArgumentException('IP and port cannot be null for isAddressUsed method.');
      
      $q = $this
        ->createQuery('s')
        ->select('count(s.id)')
        ->where('s.ip = ?', $ip)
      ->andWhere('s.port = ?', $port)
      ->andWhere('s.status != ?', Server::STATUS_INACTIVE);

      $c = (int) $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      
      return $c != 0;
    }
    
    /**
      Finds a server in need of verifying by its (or its server_group's) slug.
    */
    public function findVerifyServerBySlugAndOwner($slug, $owner_id) {
      $q = $this
        ->createQuery('s')
        ->leftJoin('s.ServerGroup sg')
        ->where('s.status = ?', Server::STATUS_NOT_VERIFIED)
        ->andWhere('sg.owner_id = ?', $owner_id)
        ->andWhere('sg.slug = ?', $slug)
        ->orWhere('s.slug = ?', $slug);
       return $q->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
    }
    
    public function findServerBySlugAndOwner($slug, $owner_id) {
      $q = $this
        ->createQuery('s')
        ->leftJoin('s.ServerGroup sg')
        ->where('s.status != ?', Server::STATUS_INACTIVE)
        ->andWhere('sg.owner_id = ?', $owner_id)
        ->andWhere('sg.slug = ?', $slug)
        ->orWhere('s.slug = ?', $slug);
       return $q->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
    }
    
    public function findServerBySlug($slug) {
      $q = $this
        ->createQuery('s')
        ->leftJoin('s.ServerGroup sg')
        ->andWhere('sg.slug = ?', $slug)
        ->orWhere('s.slug = ?', $slug);
       return $q->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
    }
}
