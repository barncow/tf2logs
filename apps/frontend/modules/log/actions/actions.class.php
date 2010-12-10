<?php

/**
 * log actions.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class logActions extends sfActions {
  public function executeIndex(sfWebRequest $request) {
    $this->logs = Doctrine::getTable('Log')->findAll();
    
    $this->form = new LogForm();
    
  }
  
  public function executeShow(sfWebRequest $request) {
    $this->log = Doctrine::getTable('Log')->getLogById($request->getParameter('id'));
     $this->forward404Unless($this->log);
  }
  
  public function executeAdd(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LogForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /*public function executeNew(sfWebRequest $request) {
    $this->form = new LogForm();
  }

  public function executeCreate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LogForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('index');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404Unless($log = Doctrine_Core::getTable('Log')->find(array($request->getParameter('id'))), sprintf('Object log does not exist (%s).', $request->getParameter('id')));
    $this->form = new LogForm($log);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($log = Doctrine_Core::getTable('Log')->find(array($request->getParameter('id'))), sprintf('Object log does not exist (%s).', $request->getParameter('id')));
    $this->form = new LogForm($log);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request) {
    $request->checkCSRFProtection();

    $this->forward404Unless($log = Doctrine_Core::getTable('Log')->find(array($request->getParameter('id'))), sprintf('Object log does not exist (%s).', $request->getParameter('id')));
    $log->delete();

    $this->redirect('log/index');
  }*/

  protected function processForm(sfWebRequest $request, sfForm $form) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($this->form->isValid()) {
      $lastid = null;
      foreach ($request->getFiles($form->getName()) as $uploadedFile) {
        $uploadDir = sfConfig::get('sf_upload_dir') . '/logs';
        move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . "/" . $uploadedFile["name"]);
        $logParser = new LogParser();
        $log = $logParser->parseLogFile($uploadDir . "/" . $uploadedFile["name"]);
        $lastid = $log->getId();
      }
      $this->redirect('log/show?id='.$lastid);
    }
  }
}
