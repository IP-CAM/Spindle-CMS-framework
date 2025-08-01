<?php
/*
 * Spindle CMS
 * Copyright (c) 2025. All rights reserved.
 *
 * This file is part of the Spindle CMS project — a lightweight, modular PHP content framework derived from OpenCart.
 *
 * @license GNU General Public License v3.0 (GPL-3.0-or-later)
 * @link    https://github.com/RandomCoderTinker/Spindle
 */

use Spindle\System\Engine\Loader;
use Spindle\System\Engine\Factory;
use Spindle\System\Engine\Registry;
use Spindle\System\Engine\Autoloader;
use Spindle\System\Engine\Config;
use Spindle\system\library\loggers\Log;
use Spindle\system\library\http\Request;
use Spindle\system\library\http\Response;
use Spindle\System\Library\DB\DatabaseManager;

// Debug/Logging Setup
if (DEVELOPMENT) {
	ini_set('log_errors', 1);
	ini_set('error_log', DIR_LOGS . 'error.log');
	error_reporting(E_ALL);
}

// Start Autoloader
$autoloader = new Autoloader();
$autoloader->register('Spindle\\', MAIN_WEB_ROOT);
$autoloader->register('Spindle\Shared\\', MAIN_SHARED_ROOT);

// Registry
$registry = new Registry();
$registry->set('autoloader', $autoloader);

// Config
$config = new Config(DIR_CONFIG);
$config->load('default.config');

// Overwrites Default Config for the application specific
$config->load('config.' . $config->get('application'));

// Set the application
$config->set('application', $config->get('application'));

// Set config in registry
$registry->set('config', $config);

// Start logging
$log = new Log(ERROR_FILE_NAME);
$registry->set('log', $log);

// Clear log on each load if in development mode
if ($config->get('development')) {
	$log->clearLog();
}

// Create the database connections
$manager = new DatabaseManager($registry);
$manager->loadDatabases($config->get('databases'), 'website'); // <- website is the default DB

// Factory
$registry->set('factory', new Factory($registry));

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$registry->set('response', $response);