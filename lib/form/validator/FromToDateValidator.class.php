<?php
/**
Loosely based on the sfValidatorSchemaCompare class in Symfony.
If a form has a start date (from date) and an end date (to date), make sure that startDate is before endDate.
*/
class FromToDateValidator extends sfValidatorSchema {
  public function __construct($options = array(), $messages = array()) {

    $this->addOption('throw_global_error', false);

    parent::__construct(null, $options, $messages);
  }
  
  protected function doClean($values) {
    if (null === $values) {
      $values = array();
    }

    if (!is_array($values)) {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $startDate  = isset($values['from_date']) ? $values['from_date'] : null;
    $endDate = isset($values['to_date']) ? $values['to_date'] : null;

    if ($startDate && $endDate && $startDate > $endDate) {
      $error = new sfValidatorError($this, 'invalid', array(
        'from_date'  => $startDate,
        'to_date' => $endDate
      ));
      if ($this->getOption('throw_global_error')) {
        throw $error;
      }

      throw new sfValidatorErrorSchema($this, array('to_date' => $error));
    }

    return $values;
  }
  
  /**
   * @see sfValidatorBase
   */
  public function asString($indent = 0)
  {
  }
}
?>
