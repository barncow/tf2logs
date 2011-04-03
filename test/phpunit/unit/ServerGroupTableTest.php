<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_ServerGroupTableTest extends sfPHPUnitBaseTestCase {
  public function testIsServerSlugUsedInGroup() {
    $s = new Server();
    $ip = '1.1.1.2';
    $port = 1234;
    $group_slug = 'mygroup';
    $group_name = 'mygroupname';
    $server_slug = 'slug';
    $server_name = 'slugname';
    
    $s->saveNewGroupServer($group_name, $group_slug, $server_name, $server_slug, $ip, $port, 1);
    
    $this->assertTrue(Doctrine::getTable('ServerGroup')->isServerSlugUsedInGroup($group_slug, $server_slug), 'Testing that a matching server slug within group will register true.');
    $this->assertFalse(Doctrine::getTable('ServerGroup')->isServerSlugUsedInGroup($group_slug, $server_slug.'blah'), 'Testing that a non-matching server slug within group will register false.');
    $this->assertFalse(Doctrine::getTable('ServerGroup')->isServerSlugUsedInGroup($group_slug.'blah', $server_slug), 'Testing that a server slug within non-existent group will register false.');
  }
}
