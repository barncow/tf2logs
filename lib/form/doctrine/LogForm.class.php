<?php

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
      'name'       => new CleanStringValidator(array('max_length' => 100, 'required'   => false)),
      'map_name'   => new CleanStringValidator(array('max_length' => 50, 'required' => false)),
      'logfile'    => new sfValidatorFile(array(
                        'required'   => true,
                        'path'       => sfConfig::get('sf_upload_dir').'/logs',
                        'mime_types' => array('text/plain', 'text/x-pascal'),
                      ))
    ));
    
    $this->widgetSchema->setNameFormat('log[%s]');
   
  }
  
  
}
