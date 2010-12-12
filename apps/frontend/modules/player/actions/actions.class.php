<?php

/**
 * player actions.
 *
 * @package    tf2logs
 * @subpackage player
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class playerActions extends sfActions {
  public function executeShowNumericSteamId(sfWebRequest $request) {
    $this->setTemplate('show');
    $this->player = Doctrine::getTable('Player')->getPlayerStatsByNumericSteamid($request->getParameter('id'));
    $this->roles = Doctrine::getTable('Player')->getPlayerRolesByNumericSteamid($request->getParameter('id'));
    $this->forward404Unless($this->player);
  }
}
