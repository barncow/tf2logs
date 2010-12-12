<?php

/**
 * Weapon form.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class WeaponForm extends BaseWeaponForm {
  
  public function configure() {
    unset($this->widgetSchema['stats_list']);
    unset($this->validatorSchema['stats_list']);
  }
}
