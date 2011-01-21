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
    $this->roles = Doctrine::getTable('Player')->getPlayerRolesByNumericSteamid($request->getParameter('id'));
    $this->weapons = Doctrine::getTable('Weapon')->getWeaponsForPlayerId($request->getParameter('id'));
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getPlayerWeaponStatsByNumericSteamid($request->getParameter('id'));
    $this->participatedLogs = Doctrine::getTable('Log')->getParticipantLogsByPlayerNumericSteamid($request->getParameter('id'));
    $this->submittedLogs = Doctrine::getTable('Log')->getSubmittedLogsByPlayerNumericSteamid($request->getParameter('id'));
    $this->numSubmittedLogs = Doctrine::getTable('Log')->getNumberSubmittedLogsByPlayerNumericSteamid($request->getParameter('id'));
  }
  
  public function executeSearch(sfWebRequest $request) {
    $param = $request->getParameter('param');
    if($param == null || $param == "") {
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
      
      $players = Doctrine::getTable('Player')->findPlayerForGivenNamePartial($param);
      if($players && count($players) > 0) {
        $this->results = $players;
      } else {
        $this->getUser()->setFlash('error', 'No player could be found for the given search.');
      }
    }
    
    $this->param = $param;
  }
  
  //////////////////////AUTH ACTIONS///////////////////////////////
  
  //todo move to app config
  const STEAM_OPENID_URL = "http://steamcommunity.com/openid";
  const PLAYER_ID_ATTR = "playerId";
  
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
    $this->openID = $this->getRedirectHtml(self::STEAM_OPENID_URL);
    $this->redirect($this->openID['url']);
  }
  
  public function executeLogout() {
    $this->getUser()->setAuthenticated(false);
    $this->getResponse()->setCookie('known_openid_identity', '');
    $this->getUser()->setAttribute(self::PLAYER_ID_ATTR, '');
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
    $player->setName($steamwebapi->getPlayerName($steamid));
    $player->save();
    
    $this->getUser()->addCredential($player->getCredential());
    $this->getUser()->setAttribute(self::PLAYER_ID_ATTR, (int)$player->getId());
    
    //symfony credential system should handle what a user has access to. Just assume it's ok.
    $this->getUser()->setFlash('notice', 'Successfully logged in.');
    $this->getUser()->setAuthenticated(true);
    sfContext::getInstance()->getResponse()->setCookie('known_openid_identity',$openid_validation_result['identity']);
    
    $back = $this->getUser()->getAttribute('openid_real_back_url');
    $this->getUser()->getAttributeHolder()->remove('openid_real_back_url');
    if (empty($back)) $back = '@controlpanel';
    $this->redirect($back);
  }
}
