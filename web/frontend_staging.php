<?php

//12/3/2010 - dirty hack to strip trailing slash since nginx won't do it
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], "/");
$_SERVER['PATH_INFO'] = rtrim($_SERVER['PATH_INFO'], "/");

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'staging', false);
sfContext::createInstance($configuration)->dispatch();
