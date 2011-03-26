<?php

/**
 * (temporary) module for displaying simple HTML pages.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class contentActions extends sfActions {
  public function executeWhatsNew(sfWebRequest $request) {
  }
  
  public function executePlugins(sfWebRequest $request) {
    $this->supStatsHits = Doctrine_Core::getTable('Track')->getHitsByUrl(sfConfig::get('app_supstats_dl_url'));
  }
  
  public function executeFaq(sfWebRequest $request) {
  }
}
