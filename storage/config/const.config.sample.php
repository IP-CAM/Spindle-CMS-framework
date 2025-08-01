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

// Website Information (Change to your default website location)
const MAIN_SITE_URL = "https://www.spindle.website";
const CDN_LOCATION = "https://cdn.spindle.website/";

// Main directory
const MAIN_FOLDER = '/path/to/your/project/';
const DIR_STORAGE = '/path/to/your/project/storage/';
const MAIN_WEB_ROOT = '/path/to/your/project/public_html/';
const MAIN_SHARED_ROOT = '/path/to/your/project/public_html/shared/';

// Directories
const DIR_IMAGE = MAIN_WEB_ROOT . 'cdn/images/';
const DIR_SYSTEM = MAIN_WEB_ROOT . 'system/';
const DIR_CONFIG = MAIN_FOLDER . 'storage/config/';
const DIR_CACHE = DIR_STORAGE . 'cache/';
const DIR_DOWNLOAD = DIR_STORAGE . 'download/';
const DIR_LOGS = DIR_STORAGE . 'logs/';
const DIR_TEMPLATE = MAIN_WEB_ROOT . 'app/view/';
const DIR_LANGUAGE = MAIN_WEB_ROOT . 'shared/language/';

// Main Vars
const DEVELOPMENT = TRUE;
const ERROR_DISPLAY = TRUE;
const ERROR_FILE_NAME = 'error.log';
const ENV_KEY_SECRET = '1d0e51f1bb56a82f11dd6f548dcf80b87eaa03b5af08d78b082b959637ce0854';

// Website headers
const DEFAULT_TITLE = "Spindle CMS | Lightweight Developer-Centric Content Framework";
const DEFAULT_DESCRIPTION = "Spindle is a streamlined, open-source CMS framework for PHP developers. Built from OpenCart's core structure and reimagined for modern content-driven websites, without the bloat.";
const DEFAULT_KEYWORDS = "Spindle CMS, PHP CMS Framework, OpenCart Fork, Lightweight CMS, Developer CMS, Modular PHP Framework, MVC CMS, GPLv3 CMS, Self-Hosted Content Platform";
const DEFAULT_TWITTER = "@SpindleCMS";
const DEFAULT_OGIMAGE = "og-image-spindlecms.png";