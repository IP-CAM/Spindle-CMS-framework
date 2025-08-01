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

// Get host parts
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$host_parts = explode('.', $host);

// Get the first part as subdomain (e.g. docs.spindle.website → docs)
$subdomain = (count($host_parts) >= 3) ? $host_parts[0] : 'default';

// Sanitize: only allow a-z, 0-9, underscore, dash
if (!preg_match('/^[a-z0-9_-]+$/i', $subdomain)) {
	$subdomain = 'default';
}

// Set main vars
$_['site_url'] = 'https://' . $subdomain . '.spindle.website/';
$_['application'] = $subdomain;
$_['subdomain'] = $subdomain;

// Folder Route
$_['main_folder'] = MAIN_WEB_ROOT . 'subDomains/' . $subdomain;

// Database

// Response
$_['response_header'] = ['Content-Type: text/html; charset=utf-8'];
$_['response_compression'] = 0;

$_['action_pre_action'] = [
	'system/settings',
	'system/seo_url',
	'system/language',
	'system/application',
	'system/event',
	'system/maintenance',
];

$_['action_event'] = [
	'controller/*/before' => [
		0 => 'event/language.before',
	],
	'controller/*/after' => [
		0 => 'event/language.after',
	],
	'view/*/before' => [
		998 => 'event/language',
	],
	'language/*/after' => [
		0 => 'system/language.after',
	],
];