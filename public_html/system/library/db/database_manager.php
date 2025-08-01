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

namespace Spindle\System\Library\DB;

use Exception;
use InvalidArgumentException;
use Spindle\System\Engine\Registry;
use Spindle\System\Library\DB\Database;

class DatabaseManager
{
	protected Registry $registry;

	public function __construct (Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * Loads and registers multiple database connections into the registry.
	 *
	 * @param array  $dbConfig Full configuration array.
	 * @param string $default  The default connection key (registered as "db").
	 */
	public function loadDatabases (array $dbConfig, string $default = 'website'): void
	{
		$shared = [
			'hostname' => $dbConfig['hostname'] ?? 'localhost',
			'port' => $dbConfig['port'] ?? '3306',
		];

		foreach ($dbConfig as $key => $config) {
			if (!is_array($config)) continue;

			$merged = array_merge($shared, $config);

			if (!isset($merged['username'], $merged['password'], $merged['database'])) {
				throw new InvalidArgumentException("Missing database config for '{$key}'");
			}

			// Set persistent flag default to false if not set
			$persistent = $merged['persistent'] ?? FALSE;

			$db = $this->createDb($merged, $persistent);

			$this->registry->set('db_' . $key, $db);

			if ($key === $default) {
				$this->registry->set('db', $db);
			}
		}
	}

	/**
	 * Creates a new Database instance with the given config.
	 *
	 * @param array $conf Database connection details.
	 *
	 * @return Database
	 * @throws Exception
	 */
	protected function createDb (array $conf, bool $persistent = FALSE): Database
	{
		return new Database(
			$conf['hostname'],
			$conf['username'],
			$conf['password'],
			$conf['database'],
			$conf['port'],
			$persistent
		);
	}

}
