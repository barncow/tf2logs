<?php

/**
 * default actions.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class defaultActions extends sfActions {
  public function executeError404() {
    $this->getUser()->setFlash('error', 'The page that you were looking for could not be found.');
  }
  
  /**
 * Warning page for restricted area with invalid credentials - requires login
 *
   */
  public function executeSecure() {
    $this->getUser()->setFlash('error', 'You are not allowed to access that page.');
  }

  /**
   * Module disabled
   *
   */
  public function executeDisabled(){
    $this->getUser()->setFlash('error', 'This website is currently down for routine maintenance. Please try again later.');
  }
}
