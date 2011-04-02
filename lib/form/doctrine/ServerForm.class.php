<?php

/**
 * Server form.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ServerForm extends BaseServerForm {
  public function configure() {
    unset($this->widgetSchema['server_group_id']);
    unset($this->validatorSchema['server_group_id']);
    unset($this->widgetSchema['last_message']);
    unset($this->validatorSchema['last_message']);
    unset($this->widgetSchema['validate_key']);
    unset($this->validatorSchema['validate_key']);
    unset($this->widgetSchema['status']);
    unset($this->validatorSchema['status']);
  }
}
