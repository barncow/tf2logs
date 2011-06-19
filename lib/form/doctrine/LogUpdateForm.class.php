<?php

/**
Extension of sfValidatorString to clean up whitespace and ASCII control chars.
*/
class CleanStringValidatorTempp extends sfValidatorString {
  public function __construct($options = array(), $messages = array()) {

    $this->addOption('throw_global_error', false);

    parent::__construct($options, $messages);
  }
  
  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);
  }
  
  protected function doClean($value) {
    
    $clean = (string) $value;
    $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $clean);
    $clean = trim($clean);

    return parent::doClean($clean);
  }
}

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
    
    $this->validatorSchema['name'] = new CleanStringValidatorTempp(array('min_length' => 1, 'max_length' => 100));
    $this->validatorSchema['map_name'] = new CleanStringValidatorTempp(array('max_length' => 50, 'required' => false));
  }
  
  
}
