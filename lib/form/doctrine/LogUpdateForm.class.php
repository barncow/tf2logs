<?php

/**
 * Form to update a log.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 */
class LogUpdateForm extends BaseLogForm
{
  public function configure() {
    unset($this->widgetSchema['redscore']);
    unset($this->validatorSchema['redscore']);
    unset($this->widgetSchema['bluescore']);
    unset($this->validatorSchema['bluescore']);
    unset($this->widgetSchema['elapsed_time']);
    unset($this->validatorSchema['elapsed_time']);
    unset($this->widgetSchema['submitter_player_id']);
    unset($this->validatorSchema['submitter_player_id']);
    unset($this->widgetSchema['error_log_name']);
    unset($this->validatorSchema['error_log_name']);
    unset($this->widgetSchema['error_exception']);
    unset($this->validatorSchema['error_exception']);
    unset($this->widgetSchema['views']);
    unset($this->validatorSchema['views']);
    unset($this->widgetSchema['created_at']);
    unset($this->validatorSchema['created_at']);
    unset($this->widgetSchema['updated_at']);
    unset($this->validatorSchema['updated_at']);
  }
  
  
}
