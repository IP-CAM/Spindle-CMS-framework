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

/** @var Spindle\system\Library\Loggers\Log $log */
/** @var Spindle\system\Library\Http\Response $response */
/** @var Spindle\System\Engine\Registry $registry */
/** @var Spindle\System\Engine\Config $config */

/** @var Spindle\System\Engine\Event $event */

// Include the required vendors for the autoloading shite
use Spindle\System\Engine\Action;
use Spindle\System\Library\Security\Csp;
use Spindle\System\Library\Http\Document;
use Spindle\System\Library\Security\Csrf;
use Spindle\System\Library\Template\Template;
use Spindle\System\Library\Language\Language;
use Spindle\System\Library\Security\Session;

require_once(DIR_SYSTEM . 'vendor.php');

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $value) {
		foreach ($value as $priority => $action) {
			$event->register($key, new Action($action), $priority);
		}
	}
}

// Set response headers from the config files
foreach ($config->get('response_header') as $header) {
	$response->addHeader($header);
}

// CSP headers
$registry->set('csp', new Csp());
$csp = $registry->get('csp');
$csp->generateNonce();

// Set response compression based on configuration
$response->setCompression((int)$config->get('response_compression'));

// Session Handler
$session = new Session($registry, $config->get('session_expire'));
$registry->set('session', $session);

// Get session parameters from config
$session_name = $config->get('session_name') ?? 'spindle_id';
$session_path = $config->get('session_path') ?? '/';
$session_domain = $config->get('session_domain') ?? '';
$session_expire = $config->get('session_expire') ?? 3600;
$session_samesite = $config->get('session_samesite') ?? 'Strict';

// Set custom session name
session_name($session_name);

// Set session cookie parameters (this auto-sets secure cookie headers)
session_set_cookie_params([
	'lifetime' => $session_expire,
	'path' => $session_path,
	'domain' => $session_domain,
	'secure' => TRUE,
	'httponly' => TRUE,
	'samesite' => $session_samesite,
]);

// Restore session ID from cookie if present
if (isset($request->cookie[$session_name])) {
	session_id($request->cookie[$session_name]);
}

// Start the session
$session->start();

// Template
$template = new Template($session);
$template->addPath(DIR_TEMPLATE);                          // Default path
$template->addPath('shared', MAIN_WEB_ROOT . 'shared/view/');
$registry->set('template', $template);

// Language
$language = new Language($config->get('language_code'));
$language->addPath(DIR_LANGUAGE);
$language->load('default');
$registry->set('language', $language);

// Document
$registry->set('document', new Document($csp));

// CSRF
$registry->set('csrf', new Csrf($session));

// Finalise
require DIR_SYSTEM . 'engine/finalise.php';
