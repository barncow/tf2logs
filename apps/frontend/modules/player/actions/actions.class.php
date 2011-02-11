<?php

/**
 * player actions.
 *
 * @package    tf2logs
 * @subpackage player
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class playerActions extends BasesfPHPOpenIDAuthActions {

  public function executeShowNumericSteamId(sfWebRequest $request) {
    $this->setTemplate('show');
    $this->player = Doctrine::getTable('Player')->getPlayerStatsByNumericSteamid($request->getParameter('id'));
    $this->forward404Unless($this->player);
    Doctrine::getTable('Player')->incrementViews($request->getParameter('id'));
    $this->roles = Doctrine::getTable('Player')->getPlayerRolesByNumericSteamid($request->getParameter('id'));
    $this->weapons = Doctrine::getTable('Weapon')->getWeaponsForPlayerId($request->getParameter('id'));
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getPlayerWeaponStatsByNumericSteamid($request->getParameter('id'));
    
    $this->slPager = new sfDoctrinePager(
      'Log',
      sfConfig::get('app_max_results_per_page')
    );
    $this->slPager->setQuery(Doctrine::getTable('Log')->getSubmittedLogsByPlayerNumericSteamidQuery($request->getParameter('id')));
    $this->slPager->setPage($request->getParameter('slPage', 1));
    $this->slPager->init();
    $this->numSubmittedLogs = count($this->slPager);
    
    $this->plPager = new sfDoctrinePager(
      'Log',
      sfConfig::get('app_max_results_per_page')
    );
    $this->plPager->setQuery(Doctrine::getTable('Log')->getParticipantLogsByPlayerNumericSteamidQuery($request->getParameter('id')));
    $this->plPager->setPage($request->getParameter('plPage', 1));
    $this->plPager->init();
  }
  
  public function executeSearch(sfWebRequest $request) {
    $param = $request->getParameter('param');
    if($param === null) {
      return;//no param set, just go to search form.
    }
    if(trim($param) == "") {
      $this->getUser()->setFlash('error', 'You must enter search criteria.');
      $this->param = "";
      return; //no param set, just go to search form.
    } else if(is_numeric($param) && strpos($param, "7656119") == 0) {
      //numeric steam id given - it is numeric and starts with above string.
      $p = Doctrine::getTable('Player')->findOneByNumericSteamid($param);
      if($p) {
        $this->redirect('@player_by_numeric_steamid?id='.$p->getNumericSteamid());
      } else {
        $this->getUser()->setFlash('error', 'The player specified could not be found.');
      }
    } else if(strpos($param, "STEAM_") === 0) {
      //steam id string
      $p = Doctrine::getTable('Player')->findOneBySteamid($param);
      if($p) {
        $this->redirect('@player_by_numeric_steamid?id='.$p->getNumericSteamid());
      } else {
        $this->getUser()->setFlash('error', 'The player specified could not be found.');
      }
    } else {
      //just do a name search.
      
      $this->pager = new sfDoctrinePager(
          'Player',
          sfConfig::get('app_max_results_per_page')
        );
      $this->pager->setQuery(Doctrine::getTable('Player')->findPlayerForGivenNamePartialQuery($param));
      $this->pager->setPage($request->getParameter('page', 1));
      $this->pager->init();
      
      if(count($this->pager->getResults()) == 1) {
        $r = $this->pager->getResults();
        $r = $r[0];
        $this->getUser()->setFlash('notice', 'Since your search returned only one result, you were automatically sent to it.');
        $this->redirect('@player_by_numeric_steamid?id='.$r->getNumericSteamid());
      }
        
      if(count($this->pager->getResults()) == 0) {
        $this->getUser()->setFlash('error', 'No results found.');
      }
    }
    
    $this->param = $param;
  }
  
  //////////////////////AUTH ACTIONS///////////////////////////////  
  public function executeOpenidError() {
    $this->error = $this->getRequest()->getErrors();
    $this->getResponse()->setCookie('known_openid_identity', '');
  }
  
  public function executeControlPanel() {
  
  }
  
  //this will send the user directly to the url, instead of bringing up a page first.
  public function executeAutoLogin() {
    //todo check if already logged in
    $this->getUser()->setAttribute('openid_real_back_url', $this->getRequest()->getReferer());
    $this->openID = $this->getRedirectHtml(sfConfig::get('app_steam_openid_url'));
    $this->redirect($this->openID['url']);
  }
  
  public function executeLogout() {
    $this->getUser()->setAuthenticated(false);
    $this->getResponse()->setCookie('known_openid_identity', '');
    $this->getUser()->setAttribute(sfConfig::get('app_playerid_session_var'), '');
    $this->getUser()->clearCredentials();
    $this->getUser()->setFlash('notice', 'You were successfully logged out.');
    $this->redirect('@homepage');
  }
  
  public function openIDCallback($openid_validation_result) {
    $parsingUtils = new ParsingUtils();
    $steamid = $parsingUtils->getNumericSteamidFromOpenID($openid_validation_result['identity']);
    $player = Doctrine::getTable('Player')->findOneByNumericSteamid($steamid);
    if(!$player) {
      $player = new Player();
      $player->setNumericSteamid($steamid);
    }
    $steamwebapi = new SteamWebAPI();
    $name = $steamwebapi->getPlayerName($steamid);
    if($name && trim($name) != "") {
      $player->setName($name);
    }
    $player->save();
    
    $this->getUser()->addCredential($player->getCredential());
    $this->getUser()->setAttribute(sfConfig::get('app_playerid_session_var'), (int)$player->getId());
    
    //symfony credential system should handle what a user has access to. Just assume it's ok.
    $this->getUser()->setFlash('notice', 'You were successfully logged in.');
    $this->getUser()->setAuthenticated(true);
    sfContext::getInstance()->getResponse()->setCookie('known_openid_identity',$openid_validation_result['identity']);
    
    $back = $this->getUser()->getAttribute('openid_real_back_url');
    $this->getUser()->getAttributeHolder()->remove('openid_real_back_url');
    if (empty($back)) $back = '@controlpanel';
    $this->redirect($back);
  }
}
