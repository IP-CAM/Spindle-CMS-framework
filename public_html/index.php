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

// Version & Framework
const VERSION = '1.0.0.0';

// Set Timezone if not set
if (!ini_get('date.timezone')) date_default_timezone_set('UTC');

// Configuration
$constPath = realpath(dirname(__DIR__) . '/storage/config/const.config.php');
if (is_file($constPath)) {
	require_once $constPath;
}

// Load Core
require_once(__DIR__ . '/system/engine/autoloader.php');
require_once(__DIR__ . '/system/engine/config.php');

// Require the init
require_once(__DIR__ . '/system/engine/init.php');