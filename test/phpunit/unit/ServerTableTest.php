<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_ServerTableTest extends sfPHPUnitBaseTestCase {
  public function testIsAddressUsed() {
    $server = new Server();
    $ip = '1.1.1.1';
    $port = 1234;
    $server->saveNewSingleServer('slug', 'name', $ip, $port, 1);
    
    $this->assertTrue(Doctrine::getTable('Server')->isAddressUsed($ip, $port), 'Testing that a matching server IP/Port will register true.');
    $this->assertFalse(Doctrine::getTable('Server')->isAddressUsed($ip, 9874), 'Testing that a non-matching server IP/Port will register false.');
  }
  
  public function testFindVerifyServerBySlugAndOwner() {
    $server = new Server();
    $slug = 'verifyserver';
    $ip = '1.1.1.1';
    $port = 9876;
    $owner_id = 1;
    $server->saveNewSingleServer($slug, 'name', $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($slug, $owner_id);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slug');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($slug, $owner_id+1);
    $this->assertFalse($find, 'assert that finding a valid server with incorrect owner_id returns false');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner('blah', $owner_id);
    $this->assertFalse($find, 'assert that a non-existent server cannot be found');
  }
  
  public function testFindVerifyServerBySlugsAndOwner() {
    $server = new Server();
    $slug = 'verifyserver';
    $ip = '1.1.1.3';
    $port = 9876;
    $owner_id = 1;
    $group_slug = 'mygroup';
    $group_name = 'mygroupname';
    $server_slug = 'slug';
    $server_name = 'slugname';
    
    $s->saveNewGroupServer($group_name, $group_slug, $server_name, $server_slug, $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugsAndOwner($group_slug, $server_slug, $owner_id);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slugs');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugsAndOwner($group_slug, $server_slug, $owner_id+1);
    $this->assertFalse($find, 'assert that finding a valid server with incorrect owner_id returns false');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugsAndOwner('blah', 'blah', $owner_id);
    $this->assertFalse($find, 'assert that a non-existent server cannot be found');
  }
  
  public function testFindServerBySlugAndOwner() {
    $server = new Server();
    $slug = 'newserver';
    $ip = '1.1.1.1';
    $port = 6589;
    $owner_id = 1;
    $server->saveNewSingleServer($slug, 'name', $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findServerBySlugAndOwner($slug, $owner_id);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slug');
    
    $find = Doctrine::getTable('Server')->findServerBySlugAndOwner($slug, $owner_id+1);
    $this->assertFalse($find, 'assert that finding a valid server with incorrect owner_id returns false');
  }
  
  public function testFindServerBySlug() {
    $server = new Server();
    $slug = 'newserver2';
    $ip = '1.1.1.1';
    $port = 5896;
    $owner_id = 1;
    $server->saveNewSingleServer($slug, 'name', $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findServerBySlug($slug);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slug');
    
    $find = Doctrine::getTable('Server')->findServerBySlug($slug.'invalid');
    $this->assertFalse($find, 'assert that finding a server with incorrect slug returns false');
  }
}
