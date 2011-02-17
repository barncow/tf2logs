<?php

/**
 * log actions.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class logActions extends sfActions {
  protected static $SEED_MAPS = array(
    "cp_badlands" => "cp_badlands"
    ,"cp_coldfront" => "cp_coldfront"
    ,"cp_fastlane" => "cp_fastlane"
    ,"cp_freight_final1" => "cp_freight_final1"
    ,"cp_5gorge" => "cp_5gorge"
    ,"cp_gorge" => "cp_gorge"
    ,"cp_granary" => "cp_granary"
    ,"cp_gravelpit" => "cp_gravelpit"
    ,"cp_gullywash_imp3" => "cp_gullywash_imp3"
    ,"cp_steel" => "cp_steel"
    ,"cp_yukon_final" => "cp_yukon_final"
    ,"cp_well" => "cp_well"
    ,"ctf_doublecross" => "ctf_doublecross"
    ,"ctf_turbine" => "ctf_turbine"
    ,"koth_sawmill" => "koth_sawmill"
    ,"koth_viaduct" => "koth_viaduct"
    ,"pl_badwater" => "pl_badwater"
    ,"pl_goldrush" => "pl_goldrush"
    ,"pl_hoodoo_final" => "pl_hoodoo_final"
    ,"pl_upward" => "pl_upward"
  );
  
  public function executeIndex(sfWebRequest $request) {
    $this->logs = Doctrine::getTable('Log')->getMostRecentLogs();
    $this->topuploaders = Doctrine::getTable('Player')->getTopUploaders();
    $this->mapNames = array();
    Doctrine::getTable('Log')->getMapsAsList($this->mapNames, self::$SEED_MAPS);
    $this->form = new LogForm();
  }
  
  public function executeShow(sfWebRequest $request) {
    $this->log = Doctrine::getTable('Log')->getLogByIdAsArray($request->getParameter('id'));
    $this->forward404Unless($this->log);
    Doctrine::getTable('Log')->incrementViews($request->getParameter('id'));
    $this->weapons = Doctrine::getTable('Weapon')->getMiniWeaponsForLogId($request->getParameter('id'));
    $this->weaponStats = Doctrine::getTable('WeaponStat')->getWeaponStatsForLogId($request->getParameter('id'));  
    $this->playerStats = Doctrine::getTable('PlayerStat')->getPlayerStatsForLogId($request->getParameter('id'));
  }
  
  public function executeEvents(sfWebRequest $request) {
    $this->events = Doctrine::getTable('Event')->getEventsByIdAsArray($request->getParameter('id'));
  }
  
  public function executeLogfile(sfWebRequest $request) {
    $this->logfile = Doctrine::getTable('LogFile')->findOneByLogId($request->getParameter('id'));
    $this->forward404Unless($this->logfile);
    $this->getResponse()->setContentType('text/plain');
    return $this->renderText($this->logfile->getLogData());
  }
  
  public function executeAdd(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LogForm();

    $status = $this->processForm($request);
    
    if($request->isXmlHttpRequest()) {
       $request->setRequestFormat('json');
      if($status == "error") {
        $this->msg = $this->getUser()->getFlash('error');
        $this->getUser()->setFlash('error', null);
      }
      else {
        $this->url = $status;
      }
    } else {
      if($status == "error") return sfView::ERROR;
      else {
        $this->redirect($status);
      }
    }
  }
  
  public function executeSearch(sfWebRequest $request) {
    $this->form = new LogSearchForm();
    if($request->getParameter('page')) {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid()) {
        $this->pager = new sfDoctrinePager(
          'Log',
          sfConfig::get('app_max_results_per_page')
        );
        $this->pager->setQuery(Doctrine::getTable('Log')->getLogsFromSearch($this->form->getValue('name'), $this->form->getValue('map_name'), $this->form->getValue('from_date'), $this->form->getValue('to_date')));
        $this->pager->setPage($request->getParameter('page', 1));
        $this->pager->init();
        
        if(count($this->pager->getResults()) == 1) {
          $r = $this->pager->getResults();
          $this->getUser()->setFlash('notice', 'Since your search returned only one result, you were automatically sent to it.');
          $this->redirect('log/show?id='.$r[0]['id']);
        } else if(count($this->pager->getResults()) == 0) {
          $this->getUser()->setFlash('error', 'No results found.');
        }
      } else {
        $this->getUser()->setFlash('error', 'There was an error with your search.');
      }
    }
  }
  
  public function executeEdit(sfWebRequest $request) {
    $this->log = Doctrine::getTable('Log')->getLogById($request->getParameter('id'));
    $this->forward404Unless($this->log);
    $playerid = $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var'));
    if($this->log->getSubmitterPlayerId() != $playerid && !$this->getUser()->hasCredential('owner')) {
      $this->getUser()->setFlash('error', 'You are not authorized to edit that log file.');
      $this->redirect('@homepage', 401); //401 is http unauthorized.
    } else {
      $this->form = new LogUpdateForm($this->log);
      $this->mapNames = array();
      Doctrine::getTable('Log')->getMapsAsList($this->mapNames, self::$SEED_MAPS);
    }
  }
  
  public function executeUpdate(sfWebRequest $request) {
    $this->log = Doctrine::getTable('Log')->getLogById($request->getParameter("id"));
    $this->forward404Unless($this->log && $request->isMethod(sfRequest::POST));
    $playerid = $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var'));
    if($this->log->getSubmitterPlayerId() != $playerid && !$this->getUser()->hasCredential('owner')) {
      $this->getUser()->setFlash('error', 'You are not authorized to edit that log file.');
      $this->redirect('@homepage', 401); //401 is http unauthorized.
    } else {
      $this->form = new LogUpdateForm($this->log);
      if($this->processUpdateForm($request)) {
        $this->removeCacheForLogId($this->log->getId());
        $this->redirect('@log_edit?id='.$this->log->getId());
      } else {
        $this->mapNames = array();
        Doctrine::getTable('Log')->getMapsAsList($this->mapNames, self::$SEED_MAPS);
        $this->setTemplate('edit');
      }
    }
  }
  
  protected function removeCacheForLogId($logid) {
    $cacheManager = $this->getContext()->getViewCacheManager();
    if($cacheManager) {
    	$cacheManager->remove('log/show?id='.$logid);
    	$cacheManager->remove('log/events?id='.$logid);
    }
  }
  
  protected function processUpdateForm(sfWebRequest $request) {
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid()) {
      $this->form->save();
      $this->getUser()->setFlash('notice', "Your updates were successfully saved.");
      return true;
    } else {
      $this->getUser()->setFlash('error', 'Your update had errors. Make sure you specify a name.');
      return false;
    }
  }

  protected function processForm(sfWebRequest $request) {
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    
    if ($this->form->isValid()) {
      $lastid = null;
      foreach ($request->getFiles($this->form->getName()) as $uploadedFile) {
        $uploadDir = sfConfig::get('app_uploadedlogs');
        $upload_filename = $uploadedFile["name"];
        move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . "/" . $upload_filename);
        
        $logParser = new LogParser();
        $log = null;
        
        try {
          $log = $logParser->parseLogFile($uploadDir . "/" . $upload_filename, $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')), $this->form->getValue('name'), $this->form->getValue('map_name'));
        } catch(CorruptLogLineException $clle) {
          $this->getUser()->setFlash('error', 'The log file that you submitted is not of the proper format. tf2logs.com will only take log files from a tournament mode game.');
          return "error";
        } catch(NoDataInLogFileException $ndilfe) {
          $this->getUser()->setFlash('error', 'The log file that you submitted does not appear to have any content, and was not processed.');
          return "error";
        } catch(Exception $e) {
          rename($uploadDir . "/" . $upload_filename, sfConfig::get('app_errorlogs'). "/" . $upload_filename);
          //create a log record so the user can find his way back when the issue is fixed.
          $log = new Log();
          $name = $this->form->getValue('name');
          if(trim($name) === "") {
            $name = $upload_filename;
          }
          $log->setName($name);
          $log->setSubmitterPlayerId($this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')));
          $log->setErrorLogName($upload_filename);
          $log->setErrorException($e);
          $log->save();
          $this->logid = $log->getId();
          $this->getUser()->setFlash('error', 'An unexpected error ocurred. We have the log file that you sent, and will get the problem fixed as soon as possible.');
          return "error";
        }
        
        if($log) {
          unlink($uploadDir . "/" . $upload_filename);
        }
        
        $lastid = $log->getId();
      }
      return 'log/show?id='.$lastid;
    } else {
      $this->getUser()->setFlash('error', 'The file you sent was not valid. Be sure that you choose a TF2 server log file to upload.');
      return "error";
    }
  }
}
