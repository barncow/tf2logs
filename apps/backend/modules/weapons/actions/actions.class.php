<?php

/**
 * Actions for maintaining the weapons list.
 *
 * @package    tf2logs
 * @subpackage weapons
 * @author     Brian Barnekow
 */
class weaponsActions extends sfActions {
  public function executeIndex() {
    $this->getWeaponsForms();
    $this->newWeapon = new WeaponForm();
  }
  
  public function executeCreate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->newWeapon = new WeaponForm();

    $this->processForm($request, $this->newWeapon, 'Weapon was successfully added.', 'Weapon had validation errors and could not be added.');

    $this->getWeaponsForms();
    $this->setTemplate('index');
  }
  
  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($weapon = Doctrine_Core::getTable('Weapon')->find(array($request->getParameter('id'))), sprintf('Object Weapon does not exist (%s).', $request->getParameter('id')));
    $this->getWeaponsForms();
    
    $form = null;
    foreach($this->weaponsForms as &$wf) {
      if($wf->getObject()->getId() == $request->getParameter('id')) {
        $form = &$wf;
      }
    }

    $this->processForm($request, $form, 'Weapon was successfully updated.', 'Weapon had validation errors and could not be updated.');

    $this->newWeapon = new WeaponForm();
    $this->setTemplate('index');
  }
  
  public function executeDelete(sfWebRequest $request) {
    $request->checkCSRFProtection();

    $this->forward404Unless($weapon = Doctrine_Core::getTable('Weapon')->find(array($request->getParameter('id'))), sprintf('Object weapon does not exist (%s).', $request->getParameter('id')));
    $name = $weapon->getName();
    $weapon->delete();
    $this->getUser()->setFlash('notice', 'The "'.$name.'" weapon was successfully deleted.');
    $this->redirect('weapons/index');
  }
  
  protected function getWeaponsForms() {
    $weapons = Doctrine::getTable('weapon')->getAllWeapons();
    $this->weaponsForms = array();
    foreach($weapons as $weapon) {
      $this->weaponsForms[] = new WeaponForm($weapon);
    }
  }
  
  protected function processForm(sfWebRequest $request, sfForm &$form, $confirmationMsg, $errorMsg) {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    
    if ($form->isValid()) {
      $form->save();
      $this->getUser()->setFlash('notice', $confirmationMsg);
      $this->redirect('weapons/index');
    } else {
      $this->getUser()->setFlash('error', $errorMsg);
    }
    
  }
}
