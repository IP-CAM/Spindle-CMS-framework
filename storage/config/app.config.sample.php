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

// default Values
$_['site_url'] = 'https://www.spindle.website/';
$_['application'] = 'app';

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

$_['action_default'] = 'common/home';
$_['action_error'] = 'error/not_found';