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
      'name'      => new sfWidgetFormInputText(),
      'map_name'  => new sfWidgetFormInputText(),
      'logfile'   => new sfWidgetFormInputFile()
    ));
    
    $this->setValidators(array(
      'name'       => new sfValidatorString(array('max_length' => 100, 'required'   => false)),
      'map_name'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'logfile'    => new sfValidatorFile(array(
                        'required'   => true,
                        'path'       => sfConfig::get('sf_upload_dir').'/logs',
                        'mime_types' => array('text/plain', 'text/x-pascal'),
                      ))
    ));
    
    $this->widgetSchema->setNameFormat('log[%s]');
   
  }
  
  
}
