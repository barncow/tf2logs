<?php

/**
Extension of sfValidatorString to clean up whitespace and ASCII control chars.
*/
class CleanStringValidatorTemp extends sfValidatorString {
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
 * Form to upload a log.
 *
 * @package    tf2logs
 * @subpackage form
 * @author     Brian Barnekow
 */
class LogForm extends sfForm
{
  public function configure() {
    $this->setWidgets(array(
      'name'      => new sfWidgetFormInputText(array('label' => 'Log Name')),
      'map_name'  => new sfWidgetFormInputText(array('label' => 'Map Name')),
      'logfile'   => new sfWidgetFormInputFile(array('label' => 'Log File'))
    ));
    
    $this->setValidators(array(
      'name'       => new CleanStringValidatorTemp(array('max_length' => 100, 'required'   => false)),
      'map_name'   => new CleanStringValidatorTemp(array('max_length' => 50, 'required' => false)),
      'logfile'    => new sfValidatorFile(array(
                        'required'   => true,
                        'path'       => sfConfig::get('sf_upload_dir').'/logs',
                        'mime_types' => array('text/plain', 'text/x-pascal'),
                      ))
    ));
    
    $this->widgetSchema->setNameFormat('log[%s]');
   
  }
  
  
}
