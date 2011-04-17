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
    $this->page = $request->getParameter('page', '');
    $this->hasGroups = Doctrine::getTable('ServerGroup')->ownerHasGroups($this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
    if($this->page == 'newgroup') {
      $this->form = new ServerGroupForm();
    } else if($this->page == 'existinggroup') {
      $this->form = new ServerForm();
      $this->form->configureExistingGroup($this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
    }
  }
  
  /**
    action to save new server/server group
  */
  public function executeAdd(sfWebRequest $request) {
    $this->form = new ServerForm();
    $this->page = $request->getParameter('page', '');
    if($this->page == 'newgroup') {
      $this->form = new ServerGroupForm();
    } else if($this->page == 'existinggroup') {
      $this->form = new ServerForm();
      $this->form->configureExistingGroup($this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
    }
    $this->processForm($request, $this->form, 'Could not add the server. Check the error messages below.');
  }
  
  /**
    form to edit a server/server group
  */
  public function executeEdit(sfWebRequest $request) {
    $this->server_slug = $request->getParameter('server_slug');
    $this->group_slug = $request->getParameter('group_slug');
    $owner_id = $this->getUser()->getCurrentPlayerId();
    
    if($this->getUser()->isOwner()) $owner_id = null;
    
    if($this->group_slug && $this->server_slug) {
      //server within group
      $this->serverGroup = Doctrine::getTable('ServerGroup')->findServerGroupBySlug($this->group_slug, $owner_id);
      $this->server = Doctrine::getTable('Server')->findServerBySlug($this->server_slug, $this->group_slug, $owner_id);
      $this->forward404Unless($this->server);
      $this->form = new ServerUpdateForm($this->server);
    } else if($this->group_slug && !$this->server_slug) {
      //either group or server, depending on servergroup's group_type
      $this->server = null;
      $this->serverGroup = Doctrine::getTable('ServerGroup')->findServerGroupBySlug($this->group_slug, $owner_id);
      $this->forward404Unless($this->serverGroup);
      $this->form = new ServerGroupUpdateForm($this->serverGroup);
    } else {
      //invalid page
      $this->forward404();
    }
  }
  
  /**
    action to save edits to a server within a group
  */
  public function executeUpdateServer(sfWebRequest $request) {
    $this->server_slug = $request->getParameter('server_slug');
    $this->group_slug = $request->getParameter('group_slug');
    $owner_id = $this->getUser()->getCurrentPlayerId();
    
    if($this->getUser()->isOwner()) $owner_id = null;
    
    $this->server = Doctrine::getTable('Server')->findServerBySlug($this->server_slug, $this->group_slug, $owner_id);
    $this->forward404Unless($this->server);
    $this->form = new ServerUpdateForm($this->server);
    $this->form->configureUniqueSlugValidator($this->group_slug, $this->server_slug);
    $this->processUpdateForm($request, $this->form, 'Could not save. Check error messages below.', '@server_multi_edit?group_slug='.$this->group_slug.'&server');
  }
  
  /**
    action to save edits to a server group or single server
  */
  public function executeUpdateGroup(sfWebRequest $request) {
    $this->server_slug = $request->getParameter('server_slug');
    $this->group_slug = $request->getParameter('group_slug');
    $owner_id = $this->getUser()->getCurrentPlayerId();
    
    if($this->getUser()->isOwner()) $owner_id = null;
    
    $this->serverGroup = Doctrine::getTable('ServerGroup')->findServerGroupBySlug($this->group_slug, $owner_id);
    $this->forward404Unless($this->serverGroup);
    $this->form = new ServerGroupUpdateForm($this->serverGroup);
    $this->processUpdateForm($request, $this->form, 'Could not save. Check error messages below.', '@server_single_edit?group');
  }
  
  /**
    action to verify new server/server group
  */
  public function executeVerify(sfWebRequest $request) {
    if($request->getParameter('server_slug', null)) {
      $this->server = Doctrine::getTable('Server')->findVerifyServerBySlugsAndOwner($request->getParameter('slug'), $request->getParameter('server_slug'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')) );
    } else {
      $this->server = Doctrine::getTable('Server')->findVerifyServerBySlugAndOwner($request->getParameter('slug'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')) );
    }
    
    $this->forward404Unless($this->server);
  }
  
  /**
    action to get status of server
  */
  public function executeStatus(sfWebRequest $request) {
    $group_slug = $request->getParameter('group_slug', $request->getParameter('server_slug'));
    $this->serverGroup = Doctrine::getTable('ServerGroup')->findOneBySlug($group_slug);
    $this->server = Doctrine::getTable('Server')->findServerBySlug($request->getParameter('server_slug'), $request->getParameter('group_slug', null));
    $this->forward404Unless($this->server);
  }
  
  /**
    main landing page for a server group
  */
  public function executeMain(sfWebRequest $request) {
    $group_slug = $request->getParameter('group_slug');
    $server_slug = $request->getParameter('server_slug');
    
    $this->serverGroup = Doctrine::getTable('ServerGroup')->findOneBySlug($group_slug);
    $this->forward404Unless($this->serverGroup);
    
    if($server_slug) {
      $this->server = Doctrine::getTable('Server')->findServerBySlug($server_slug, $group_slug);
      $this->forward404Unless($this->server);
    } else {
      $this->server = null;
    }
    
    $this->topViewedLogs = Doctrine::getTable('Log')->getTopViewedLogsForServerGroup($request->getParameter('group_slug'), $request->getParameter('server_slug'));
    $this->recentlyAdded = Doctrine::getTable('Log')->getMostRecentLogsForServerGroup($request->getParameter('group_slug'), $request->getParameter('server_slug'));
  }
  
  protected function processForm(sfWebRequest $request, sfForm &$form, $errorMsg) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($form->isValid()) {
      
      if($this->page == 'single') {
        $server = new Server();
        $server->saveNewSingleServer($form->getValue('slug'), $form->getValue('name'), $form->getValue('ip'), $form->getValue('port'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
        $this->getUser()->setFlash('notice', 'Your server was successfully added. Follow the instructions to validate the server.');
        $this->redirect('@server_verify_single?slug='.$form->getValue('slug'));
      } else if ($this->page == 'newgroup') {
        $s = $form->setOwnerId($this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')))->save();
        
        $this->getUser()->setFlash('notice', 'Your server and group were successfully added. Follow the instructions to validate the server.');
        
        $this->redirect('@server_verify_group?slug='.$s->getServerGroup()->getSlug().'&server_slug='.$s->getSlug());
      } else if ($this->page == 'existinggroup') {
        $server = new Server();
        $server->saveNewServerToGroup(Doctrine::getTable('ServerGroup')->getSlugByGroupId($form->getValue('server_group_id')), $form->getValue('name'), $form->getValue('slug'), $form->getValue('ip'), $form->getValue('port'), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
        
        $this->getUser()->setFlash('notice', 'Your server was successfully added to '.$server->getServerGroup()->getName().'. Follow the instructions to validate the server.');
        
        $this->redirect('@server_verify_group?slug='.$server->getServerGroup()->getSlug().'&server_slug='.$server->getSlug());
      }
      
      
    } else {
      $this->getUser()->setFlash('error', $errorMsg);
      $this->setTemplate('new');
    }
    
  }
  
  protected function processUpdateForm(sfWebRequest $request, sfForm &$form, $errorMsg, $redirect) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($form->isValid()) {
      
      $this->form->save();
      $this->getUser()->setFlash('notice', 'Your changes were saved.');
      $this->redirect($redirect.'_slug='.$this->form->getValue('slug'));
    } else {
      $this->getUser()->setFlash('error', $errorMsg);
      $this->setTemplate('edit');
    }
    
  }
}
