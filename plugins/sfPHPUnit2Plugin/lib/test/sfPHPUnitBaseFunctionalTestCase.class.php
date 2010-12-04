<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfBasePhpunitFunctionalTestCase is the super class for all functional
 * tests using PHPUnit.
 * The "getBrowser" method provides the current functional test/browser
 * instance of symfony and you can do anything with it you are used from
 * the normal lime based tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage test
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPHPUnitBaseFunctionalTestCase extends sfPHPUnitBaseTestCase
{
  /**
   * The sfBrowser instance
   *
   * @var sfBrowser
   */
  private $testBrowser;

  /**
   * Returns flag if test case should run in debug mode
   *
   * @return bool true on default
   */
  protected function isDebug()
  {
    return true;
  }

  /**
   * setUp method for PHPUnit
   *
   */
  protected function setUp()
  {
    // Here we have to initialize the according context for the test case.
    // As this initialization is quite expensive, the script tries to
    // to do this as rare as possible.
    $app = $this->getApplication();

    if (!sfContext::hasInstance($app))
    {
      $configuration = ProjectConfiguration::getApplicationConfiguration($app, $this->getEnvironment(), $this->isDebug());
      sfContext::createInstance($configuration, $app);
      // We have to create a configuration first before the symfony lib is defined.
      // this is the only but ugly chance for including the lime lib correctly
      // without creating a project configuration instance somewhere before
      require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';
    }

    // do we have to switch the context?
    if($app != sfContext::getInstance()->getConfiguration()->getApplication())
    {
      sfContext::switchTo($app);
    }

    // autoloading ready, continue
    $this->testBrowser = new sfTestFunctional(new sfBrowser(), $this->getTest());

    $this->_start();
  }

  /**
   * tearDown method for PHPUnit
   *
   */
  protected function tearDown()
  {
    $this->_end();
  }

  /**
   * Returns the sfBrowser instance
   *
   * @return sfBrowser
   */
  public function getBrowser()
  {
    return $this->testBrowser;
  }

  /*
   * Returns sfContext instance
   *
   * @return sfContext
   */
  protected function getContext()
  {
    // a valid context is created already in the functional bootstrap file
    // there is nothing more to do here, than fetching the current context instance
    if(!$this->context)
    {
      $this->context = sfContext::getInstance();
    }

    return $this->context;
  }
}