<?php

/**
 * ItemPickupStat
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    tf2logs
 * @subpackage model
 * @author     Brian Barnekow
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ItemPickupStat extends BaseItemPickupStat {
  public static function createItemPickupStat($itemKeyName, $increment = 1) {
    return array(
      'item_key_name' => $itemKeyName,
      'times_picked_up' => $increment
    );
  }
}
