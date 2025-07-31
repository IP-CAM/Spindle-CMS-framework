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

namespace Spindle\System\Engine;

/**
 * Class Autoloader
 */
class Autoloader
{
	/**
	 * @var array<string, array<string, mixed>>
	 */
	private array $path = [];

	/**
	 * Constructor
	 */
	public function __construct ()
	{
		spl_autoload_register(function(string $class): void {
			error_log("Autoloading: $class");
			$this->load($class);
		});

		spl_autoload_extensions('.php');
	}

	/**
	 * Register
	 *
	 * @param string $namespace
	 * @param string $directory
	 * @param bool   $psr4
	 *
	 * @return void
	 *
	 * @psr-4 filename standard is stupid composer has lower case file structure than its packages have camelcase file
	 *        names!
	 */
	public function register (string $namespace, string $directory, bool $psr4 = FALSE): void
	{
		$this->path[$namespace] = [
			'directory' => $directory,
			'psr4' => $psr4,
		];
	}

	/**
	 * Load
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public function load (string $class): bool
	{
		if (DEVELOPMENT) {
			error_log("[AUTOLOAD] Attempting to load: $class");
		}

		$bestMatch = '';
		$bestFile = '';

		foreach ($this->path as $namespace => $options) {
			if (str_starts_with($class, $namespace)) {
				$relative = substr($class, strlen($namespace));
				$relativePath = str_replace('\\', '/', $relative);

				if (!$options['psr4']) {
					$relativePath = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $relativePath));
				}

				$file = $options['directory'] . '/' . trim($relativePath, '/') . '.php';

				if (DEVELOPMENT) {
					error_log("[AUTOLOAD] Testing: $namespace → $file");
				}
				if (strlen($namespace) > strlen($bestMatch) && is_file($file)) {
					$bestMatch = $namespace;
					$bestFile = $file;
				}
			}
		}

		if ($bestFile) {
			if (DEVELOPMENT) {
				error_log("[AUTOLOAD] ✅ Loading file: $bestFile");
			}
			include_once($bestFile);

			return TRUE;
		} else {
			if (DEVELOPMENT) {
				error_log("[AUTOLOAD] ❌ No matching file found for $class");
			}

			return FALSE;
		}
	}

}
