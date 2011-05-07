<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '192.168.0.197', '10.140.8.137')))
{
  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

//12/11/2010 - dirty hack to strip trailing slash since nginx won't do it
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], "/");
$_SERVER['PATH_INFO'] = rtrim($_SERVER['PATH_INFO'], "/");

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
