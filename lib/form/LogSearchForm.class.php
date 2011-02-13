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
    $this->disableLocalCSRFProtection();
    $maps = array();
    $maps[""] = "Any Map";
    Doctrine::getTable('Log')->getMapsAsList($maps);
    
    $this->setWidgets(array(
      'name'      => new sfWidgetFormInputText(array('label' => 'Log Name')),
      'map_name'  => new sfWidgetFormSelect(array('choices' => $maps, 'label' => 'Map Name')),
      'from_date' => new sfWidgetFormJQueryDate(array(
                        'culture' => 'en',
                        'label' => 'Start Date'
                      )),
      'to_date'   => new sfWidgetFormJQueryDate(array(
                        'culture' => 'en',
                        'label' => 'End Date'
                      ))
    ));
    
     $this->setValidators(array(
      'name'    => new sfValidatorString(array('required' => false)),
      'map_name' => new sfValidatorChoice(array('choices' => array_keys($maps), 'required' => false)),
      'from_date' => new sfValidatorDate(array('required' => false)),
      'to_date' => new sfValidatorDate(array('required' => false))
    ));
    
    $this->validatorSchema->setPostValidator(
      new FromToDateValidator(array('throw_global_error' => true),
        array('invalid' => 'The Start date ("%from_date%") must be before the End date ("%to_date%")')
      )
    );
    
    $this->widgetSchema->setNameFormat($this->getName().'[%s]');
    
  }
  
  public function getName() {
    return "LogSearchForm";
  }
}
?>
