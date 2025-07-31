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

use Spindle\System\Engine\Registry;
use Spindle\System\Engine\Autoloader;
use Spindle\System\Engine\Config;

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