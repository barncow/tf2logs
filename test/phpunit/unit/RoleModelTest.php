<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_RoleModelTest extends sfPHPUnitBaseTestCase {
  public function testGetRoleFromWeapon() {
    $role = Doctrine::getTable('Role')->getRoleFromWeapon("scattergun");
    $this->assertEquals('scout', $role->getKeyName(), "getting valid weapon role");
    
    $role = Doctrine::getTable('Role')->getRoleFromWeapon("sedfsdf");
    $this->assertNull($role, "invalid weapon");
  }
}
