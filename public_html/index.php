<?php

// Version & Framework
const VERSION = '1.0.0.0';

// Set Timezone if not set
if (!ini_get('date.timezone')) date_default_timezone_set('UTC');

// Configuration
$constPath = realpath(dirname(__DIR__) . '/storage/config/const.config.php');
if (is_file($constPath)) {
	require_once $constPath;
}

