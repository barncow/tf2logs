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
      'name'    => new sfWidgetFormInputText(),
      'logfile'   => new sfWidgetFormInputFile()
    ));
    
    $this->setValidators(array(
      'name'       => new sfValidatorString(array('max_length' => 100)),
      'logfile'    => new sfValidatorFile(array(
                        'required'   => false,
                        'path'       => sfConfig::get('sf_upload_dir').'/logs',
                        'mime_types' => array('text/plain'),
                      ))
    ));
    
    $this->widgetSchema->setNameFormat('log[%s]');
   
  }
  
  
}
