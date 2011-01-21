<?php

/**
 * log actions.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class logActions extends sfActions {
  //todo move to app config
  const STEAM_OPENID_URL = "http://steamcommunity.com/openid";
  const PLAYER_ID_ATTR = "playerId";
  
  public function executeIndex(sfWebRequest $request) {
    $this->logs = Doctrine::getTable('Log')->getMostRecentLogs();
    $this->topuploaders = Doctrine::getTable('Player')->getTopUploaders();
    $this->form = new LogForm(); 
  }
  
  public function executeShow(sfWebRequest $request) {
    //todo log is extremely slow. However, its the hyrdrating, not the query. May reduce when factor out events.
    $this->log = Doctrine::getTable('Log')->getLogByIdAsArray($request->getParameter('id'));
    $this->weapons = Doctrine::getTable('Weapon')->getWeaponsForLogId($request->getParameter('id'));
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getWeaponStatsForLogId($request->getParameter('id'));  
    $this->playerStats = Doctrine::getTable('PlayerStat')->getPlayerStatsForLogId($request->getParameter('id'));
    $this->forward404Unless($this->log);
  }
  
  public function executeEvents(sfWebRequest $request) {
    $this->events = Doctrine::getTable('Event')->getEventsByIdAsArray($request->getParameter('id'));
  }
  
  public function executeAdd(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LogForm();

    return $this->processForm($request, $this->form);
  }

  protected function processForm(sfWebRequest $request, sfForm $form) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($this->form->isValid()) {
      $lastid = null;
      foreach ($request->getFiles($form->getName()) as $uploadedFile) {
        $uploadDir = sfConfig::get('app_uploadedlogs');
        $upload_filename = $uploadedFile["name"];
        move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . "/" . $upload_filename);
        
        $logParser = new LogParser();
        $log = null;
        
        try {
          $log = $logParser->parseLogFile($uploadDir . "/" . $upload_filename, $this->getUser()->getAttribute(self::PLAYER_ID_ATTR), $form->getValue('name'), $form->getValue('map_name'));
        } catch(TournamentModeNotFoundException $tmnfe) {
          $this->getUser()->setFlash('error', 'The log file that you submitted is not of the proper format. tf2logs.com will only take log files from a tournament mode game.');
          return sfView::ERROR;
        } catch(CorruptLogLineException $clle) {
          $this->getUser()->setFlash('error', 'The log file that you submitted is not of the proper format. tf2logs.com will only take log files from a tournament mode game.'.$clle);
          return sfView::ERROR;
        } catch(Exception $e) {
          rename($uploadDir . "/" . $upload_filename, sfConfig::get('app_errorlogs'). "/" . $upload_filename);
          //create a log record so the user can find his way back when the issue is fixed.
          $log = new Log();
          $log->setName($form->getValue('name'));
          $log->setErrorLogName($upload_filename);
          $log->setErrorException($e);
          $log->save();
          $this->logid = $log->getId();
          $this->getUser()->setFlash('error', 'An unexpected error ocurred. We have the log file that you sent, and will get the problem fixed as soon as possible.');
          return sfView::ERROR;
        }
        
        if($log) {
          unlink($uploadDir . "/" . $upload_filename);
        }
        
        $lastid = $log->getId();
      }
      $this->redirect('log/show?id='.$lastid);
    } else {
      $this->getUser()->setFlash('error', 'The file you sent was not valid. Be sure that you choose a TF2 server log file to upload.');
      $this->redirect('log/index');
    }
  }
}
