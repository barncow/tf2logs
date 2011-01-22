<?php

/**
 * Form for searching for logs.
 * 
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 */
class LogSearchForm extends BaseForm { 
  public function configure() {
    $maps = array();
    $maps[""] = "Any Map";
    Doctrine::getTable('Log')->getMapsAsList($maps);
    
    $this->setWidgets(array(
      'name'      => new sfWidgetFormInputText(),
      'map_name'  => new sfWidgetFormSelect(array('choices' => $maps)),
      'from_date' => new sfWidgetFormJQueryDate(array(
                        'culture' => 'en'
                      )),
      'to_date'   => new sfWidgetFormJQueryDate(array(
                        'culture' => 'en'
                      ))
    ));
    
     $this->setValidators(array(
      'name'    => new sfValidatorString(array('required' => false)),
      'map_name' => new sfValidatorChoice(array('choices' => array_keys($maps), 'required' => false)),
      'from_date' => new sfValidatorDate(array('required' => false)),
      'to_date' => new sfValidatorDate(array('required' => false))
    ));
    
    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare('from_date', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'to_date',
        array('throw_global_error' => true),
        array('invalid' => 'The From date ("%left_field%") must be before the To date ("%right_field%")')
      )
    );
    
    $this->widgetSchema->setNameFormat($this->getName().'[%s]');
    
  }
  
  public function getName() {
    return "LogSearchForm";
  }
}
?>
