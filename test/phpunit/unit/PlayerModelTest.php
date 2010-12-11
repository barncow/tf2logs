<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_PlayerModelTest extends sfPHPUnitBaseTestCase {
  public function testConvertStandardToNumericSteamid() {
    $p = new Player();
    $this->assertEquals("76561197993228277", $p->convertStandardToNumericSteamid("STEAM_0:1:16481274"));
  }
  
  public function testConvertNumericToStandardSteamid() {
    $p = new Player();
    $this->assertEquals("STEAM_0:1:16481274", $p->convertNumericToStandardSteamid("76561197993228277"));
  }
}
