<?php

/**
 * servers actions.
 *
 * @package    tf2logs
 * @subpackage servers
 * @author     Brian Barnekow
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serversActions extends sfActions {
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request) {
    $this->forward('default', 'module');
  }
  
  /**
    form to create a new server/server group
  */
  public function executeNew(sfWebRequest $request) {
    $this->form = new ServerForm();
  }
  
  /**
    action to save new server/server group
  */
  public function executeAdd(sfWebRequest $request) {
    $this->form = new ServerForm();
    $this->processForm($request, $this->form, 'Your server was successfully added. Follow the instructions to validate the server.', 'Could not add the server. Check the error messages below.');
  }
  
  public function executeVerify(sfWebRequest $request) {
    $this->server = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($request->getParameter('slug'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')) );
    $this->forward404Unless($this->server);
  }
  
  public function executeStatus(sfWebRequest $request) {
    $this->server = Doctrine::getTable('Server')->findServerBySlugAndOwner($request->getParameter('slug'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')) );
    $this->forward404Unless($this->server);
  }
  
  public function executeMain(sfWebRequest $request) {
    $this->server = Doctrine::getTable('Server')->findServerBySlugAndOwner($request->getParameter('slug'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')) );
    $this->forward404Unless($this->server);
  }
  
  protected function processForm(sfWebRequest $request, sfForm &$form, $confirmationMsg, $errorMsg) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($form->isValid()) {
      
      $server = new Server();
      $server->saveNewServer($form->getValue('slug'), $form->getValue('name'), $form->getValue('ip'), $form->getValue('port'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
      
      $this->getUser()->setFlash('notice', $confirmationMsg);
      $this->redirect('@server_verify?slug='.$form->getValue('slug'));
    } else {
      $this->getUser()->setFlash('error', $errorMsg);
      $this->setTemplate('new');
    }
    
  }
}
