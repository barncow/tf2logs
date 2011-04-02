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
}
