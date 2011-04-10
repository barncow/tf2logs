<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_LogLineTableTest extends sfPHPUnitBaseTestCase {
  public function testGetOneOldLine() {
    //the getter for these likes to simplify things but returning just the value instead of the value in an array for only one result. ensure that this won't happen.
    $serverId = 1;
    $server = Doctrine::getTable('Server')->findOneById($serverId);
    
    $line = new LogLine();
    $line->setLineYear(2011);
    $line->setLineMonth(4);
    $line->setLineDay(9);
    $line->setLineHour(14);
    $line->setLineMinute(4);
    $line->setLineSecond(56);
    $line->setCreatedAt('2011-04-09 12:00:00');
    $line->setServerId($serverId);
    $line->setLineData('L 09/29/2010 - 19:08:57: "Cres<49><STEAM_0:0:8581157><Blue>" triggered "damage" (damage "22")');
    $line->save();
    
    $this->assertEquals(1, count(Doctrine::getTable('LogLine')->getLogLinesForServer($server->getIp(), $server->getPort(), 90)), 'Testing that can get one result for logline.');
  }
  
  public function testGetTwoOldLines() {
    $serverId = 1;
    $server = Doctrine::getTable('Server')->findOneById($serverId);
    
    $line = new LogLine();
    $line->setLineYear(2011);
    $line->setLineMonth(4);
    $line->setLineDay(9);
    $line->setLineHour(14);
    $line->setLineMinute(4);
    $line->setLineSecond(56);
    $line->setCreatedAt('2011-04-09 12:00:00');
    $line->setServerId($serverId);
    $line->setLineData('L 09/29/2010 - 19:08:57: "Cres<49><STEAM_0:0:8581157><Blue>" triggered "damage" (damage "22")');
    $line->save();
    
    $line2 = new LogLine();
    $line2->setLineYear(2011);
    $line2->setLineMonth(4);
    $line2->setLineDay(9);
    $line2->setLineHour(14);
    $line2->setLineMinute(4);
    $line2->setLineSecond(56);
    $line2->setCreatedAt('2011-04-09 13:00:00');
    $line2->setServerId($serverId);
    $line2->setLineData('L 09/29/2010 - 19:08:57: "Cres<49><STEAM_0:0:8581157><Blue>" triggered "damage" (damage "33")');
    $line2->save();
    
    //wayyy  in the future
    $line3 = new LogLine();
    $line3->setLineYear(2111);
    $line3->setLineMonth(4);
    $line3->setLineDay(9);
    $line3->setLineHour(14);
    $line3->setLineMinute(4);
    $line3->setLineSecond(56);
    $line3->setCreatedAt('2111-04-09 13:00:00');
    $line3->setServerId($serverId);
    $line3->setLineData('L 09/29/2010 - 19:08:57: "Cres<49><STEAM_0:0:8581157><Blue>" triggered "damage" (damage "33")');
    $line3->save();
    
    $this->assertEquals(2, count(Doctrine::getTable('LogLine')->getLogLinesForServer($server->getIp(), $server->getPort(), 90)), 'Testing that can get two results, skipping future, for logline.');
  }
}
