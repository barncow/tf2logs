<?php

/**
 * Actions for administrating logs.
 *
 * @package    tf2logs
 * @subpackage log
 * @author     Brian Barnekow
 */
class logActions extends sfActions {
  
  /**
  * Action to list unfinished logs, see what the error was, and push through if necessary.
  */
  public function executeUnfinished(sfWebRequest $request) {
    $this->logs = Doctrine::getTable('Log')->listErrorLogs();
  }
  
  /**
  * Action to regenerate an unfinished log
  */
  public function executeRegenerateUnfinished(sfWebRequest $request) {
    $log = Doctrine::getTable('Log')->getErrorLogById($request->getParameter('id'));
    $this->forward404Unless($log);
    $logParser = new LogParser();
    $log = $logParser->parseLogFile(sfConfig::get('app_errorlogs') . "/" . $log->getErrorLogName(), $this->getUser()->getAttribute(sfConfig::get('app_playerid_session_var')), null, null, $log);
    unlink(sfConfig::get('app_errorlogs') . "/" . $log->getErrorLogName());
    $log->setErrorLogName(null);
    $log->setErrorException(null);
    $log->save();
    $this->removeCacheForLogId($log->getId());
    $this->getUser()->setFlash('notice', 'Log Successfully processed.');
    $this->redirect('log/unfinished');
  }
  
  /**
  * Action to regenerate a log
  */
  public function executeRegenerate(sfWebRequest $request) {
    $log = Doctrine::getTable('Log')->getLogById($request->getParameter('id'));
    $this->forward404Unless($log);
    
    $logParser = new LogParser();
    $log = $logParser->parseLogFromDB($log);
    $this->removeCacheForLogId($log->getId());
    
    $this->getUser()->setFlash('notice', 'Log Successfully regenerated.');
    $this->redirect('authModule/controlPanel');
  }
  
  public function executeDelete(sfWebRequest $request) {
    $request->checkCSRFProtection();

    $this->forward404Unless($log = Doctrine_Core::getTable('Log')->find(array($request->getParameter('id'))), sprintf('Object log does not exist (%s).', $request->getParameter('id')));
    if($log->getErrorLogName() && file_exists(sfConfig::get('app_errorlogs') . "/" . $log->getErrorLogName())) {
      unlink(sfConfig::get('app_errorlogs') . "/" . $log->getErrorLogName());
    }
    $log->delete();

    $this->redirect('log/unfinished');
  }
  
  public function executeLogfile(sfWebRequest $request) {
    $log = Doctrine::getTable('Log')->getErrorLogById($request->getParameter('id'));
    $this->forward404Unless($log);
    $logParser = new LogParser();
    $log = file_get_contents(sfConfig::get('app_errorlogs') . "/" . $log->getErrorLogName());
    $this->getResponse()->setHttpHeader("content-type", 'text-plain');
    return $this->renderText($log);
  }
  
  protected function removeCacheForLogId($logid) {
    $frontend_cache_dir = sfConfig::get('sf_cache_dir').DIRECTORY_SEPARATOR.'frontend'. DIRECTORY_SEPARATOR.sfConfig::get('sf_environment').DIRECTORY_SEPARATOR.'template';
    $cache = new sfFileCache(array('cache_dir' => $frontend_cache_dir)); // Use the same settings as the ones defined in the frontend factories.yml
    if($cache) {
      $cache->removePattern('log/show?id='.$logid);
      $cache->removePattern('log/events?id='.$logid);
    }
  }
  
  protected function listUploadedLogs() {
    $files = array();
    if ($handle = opendir(sfConfig::get('app_errorlogs'))) {
      while (false !== ($file = readdir($handle))) {
          if ($file != "." && $file != "..") {
              $files[] = $file;
          }
      }
      closedir($handle);
    }
    return $files;
  }
}
