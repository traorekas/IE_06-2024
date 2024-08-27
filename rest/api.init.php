<?php

// ---- Initialize logging ---- //

require_once 'libs/KLogger.php';
$log = new KLogger ("log.txt" , KLogger::DEBUG);

// ---- Initialize Slim Framework ---- //

require 'libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

// ---- Initialize ActiveRecord ---- //

require_once 'libs/ActiveRecord/ActiveRecord.php';
 
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory('libs/models');
    $cfg->set_connections(array('development' => $GLOBALS['CONNECTION_STRING']));
});

// ---- Other initializations ---- //

// Initialize session.
session_start();
