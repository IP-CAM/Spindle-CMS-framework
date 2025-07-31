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

$_['databases']['hostname'] = 'localhost';
$_['databases']['port'] = 3306;

// Database
$_['databases']['website']['username'] = 'root';
$_['databases']['website']['password'] = '';
$_['databases']['website']['database'] = '';
$_['databases']['website']['persistent'] = TRUE;

// Actions
$_['action_default'] = 'common/home';
$_['action_error'] = 'error/not_found';
$_['action_pre_action'] = [];
$_['action_event'] = [];

// Session
$_['session_name'] = 'spindle_session';
$_['session_domain'] = '.' . MAIN_SITE_URL;
$_['session_path'] = !empty($_SERVER['PHP_SELF']) ? rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/' : '/';
$_['session_expire'] = 3600;
$_['session_probability'] = 1;
$_['session_divisor'] = 5;
$_['session_samesite'] = 'Strict';

// Cache
$_['cache_expire'] = 3600;

// Template
$_['template_engine'] = 'twig';
$_['template_extension'] = '.twig';

// Error
$_['error_display'] = ERROR_DISPLAY; // You need to change this to false on a live site.
$_['error_log'] = DEVELOPMENT;
$_['error_filename'] = 'error.log';
$_['error_page'] = 'error.html';

// Response
$_['response_header'] = ['Content-Type: text/html; charset=utf-8'];
$_['response_compression'] = 0;

// Language
$_['language_code'] = 'en-gb';

// Any other config needed like personal tokens, API keys etc...
$_['google_recaptcha_site'] = '';
$_['google_recaptcha_secret'] = '';