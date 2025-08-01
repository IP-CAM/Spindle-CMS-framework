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

/** @var Action $action */
/** @var Registry $registry */
/** @var Config $config */
/** @var Event $event */
/** @var Log $log */

/** @var Response $response */

use Spindle\System\Engine\Event;
use Spindle\System\Engine\Action;
use Spindle\System\Engine\Config;
use Spindle\System\Engine\registry;
use Spindle\system\library\loggers\Log;
use Spindle\system\library\http\Response;

$action = '';
$args = [];

// Action error object to execute if any other actions cannot be executed.
$error = new Action($config->get('action_error'));

// Pre Actions
foreach ($config->get('action_pre_action') as $pre_actions) {
	$pre_action = new Action($pre_actions);

	$result = $pre_action->execute($registry, $args);

	if ($result instanceof Action) {
		$action = $result;
		break;
	}

	// If action cannot be executed, we return an action error object.
	if ($result instanceof Exception) {
		$action = $error;
		error_log("----");
		error_log($result->getMessage());
		error_log("PreAction: {$pre_actions} was not found");
		error_log("----");
		// In case there is an error we only want to execute once.
		$error = '';
		break;
	}
}

// Route
if (isset($request->get['route'])) {
	$route = (string)$request->get['route'];
} else {
	$route = (string)$config->get('action_default');
}

// To block calls to controller methods we want to keep from being accessed directly
if (str_contains($route, '._')) {
	$action = new Action($config->get('action_error'));
}

if ($action) {
	$route = $action->getId();
}

// Keep the original trigger
$trigger = $route;

$args = [];

// Trigger the pre events
$event->trigger('controller/' . $trigger . '/before', [&$route, &$args]);

// Action to execute
if (!$action) {
	$action = new Action($route);
}

// Dispatch
while ($action) {
	// Execute action
	$output = $action->execute($registry, $args);

	// Make action a non-object so it's not infinitely looping
	$action = '';

	// Action object returned then we keep the loop going
	if ($output instanceof Action) {
		$action = $output;
	}

	// If action cannot be executed, we return the action error object.
	if ($output instanceof Exception) {
		$action = $error;

		// In case there is an error we don't want to infinitely keep calling the action error object.
		$error = '';
	}
}

// Trigger the post events
$event->trigger('controller/' . $trigger . '/after', [&$route, &$args, &$output]);

// Output
$response->output();