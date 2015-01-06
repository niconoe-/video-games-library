<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
$aNewIncludePath = [
    realpath(APPLICATION_PATH . '/../external/ZendFramework-1.12.9/library'), //Zend's library
    realpath(APPLICATION_PATH . '/../library'), //My library
    get_include_path() //previous include path
];
set_include_path(implode(PATH_SEPARATOR, $aNewIncludePath));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();