<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_ServerTableTest extends sfPHPUnitBaseTestCase {
  public function testIsAddressUsed() {
    $server = new Server();
    $ip = '1.1.1.1';
    $port = 1234;
    $server->saveNewServer('slug', 'name', $ip, $port, 1);
    
    $this->assertTrue(Doctrine::getTable('Server')->isAddressUsed($ip, $port), 'Testing that a matching server IP/Port will register true.');
    $this->assertFalse(Doctrine::getTable('Server')->isAddressUsed($ip, 9874), 'Testing that a non-matching server IP/Port will register false.');
  }
  
  public function testFindVerifyServerBySlugAndOwner() {
    $server = new Server();
    $slug = 'verifyserver';
    $ip = '1.1.1.1';
    $port = 9876;
    $owner_id = 1;
    $server->saveNewServer($slug, 'name', $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($slug, $owner_id);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slug');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($slug, $owner_id+1);
    $this->assertFalse($find, 'assert that finding a valid server with incorrect owner_id returns false');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner('blah', $owner_id);
    $this->assertFalse($find, 'assert that a non-existent server cannot be found');
  }
  
  public function testFindServerBySlugAndOwner() {
    $server = new Server();
    $slug = 'newserver';
    $ip = '1.1.1.1';
    $port = 6589;
    $owner_id = 1;
    $server->saveNewServer($slug, 'name', $ip, $port, $owner_id);
    
    $find = Doctrine::getTable('Server')->findServerBySlugAndOwner($slug, $owner_id);
    $this->assertEquals($slug, $find->getSlug(), 'assert that newly entered server can be retrieved by its slug');
    
    $find = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($slug, $owner_id+1);
    $this->assertFalse($find, 'assert that finding a valid server with incorrect owner_id returns false');
  }
}
