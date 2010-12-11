<?php

/**
 * authModule actions.
 *
 * @package    tf2logs
 * @subpackage authModule
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authModuleActions extends BasesfPHPOpenIDAuthActions {
  public function executeOpenidError() {
    $this->error = $this->getRequest()->getErrors();
    $this->getResponse()->setCookie('known_openid_identity', '');
  }
  
  public function executeIndex() {
  }
  
  public function executeControlPanel() {
  
  }
  
  public function executeLogin() {
    $this->openID = $this->getRedirectHtml("http://steamcommunity.com/openid");
  }
  
  public function executeLogout() {
    $this->getUser()->setAuthenticated(false);
    $this->getResponse()->setCookie('known_openid_identity', '');
    $this->getUser()->setFlash('notice', 'You were successfully logged out.');
    $this->redirect('@homepage');
  }
  
  public function openIDCallback($openid_validation_result) {
    $this->getUser()->setFlash('notice', 'Successfully logged in.');
    //$user->addCredential('foo');
    $this->getUser()->setAuthenticated(true);
    sfContext::getInstance()->getResponse()->setCookie('known_openid_identity',$openid_validation_result['identity']);
    $back = '@controlpanel';
    $this->redirect($back);
  }
}
