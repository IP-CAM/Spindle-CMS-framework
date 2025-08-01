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

use Exception;
use Spindle\System\Library\Http\Url;

/**
 * Added properties for compatibility with PHPStorm linking
 *
 * @property Url    $url   ;
 * @property Loader $load  ;
 * @property Config $config;
 *
 *
 */
class Controller
{
	/**
	 * @var Registry
	 */
	protected Registry $registry;

	/**
	 * Constructor
	 *
	 * @param Registry $registry
	 */
	public function __construct (Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * __get
	 *
	 * @param string $key
	 *
	 * @return object
	 * @throws Exception
	 */
	public function __get (string $key): object
	{
		if ($this->registry->has($key)) {
			return $this->registry->get($key);
		} else {
			throw new Exception('Error: Could not call registry key ' . $key . '!');
		}
	}

	/**
	 * __set
	 *
	 * @param string $key
	 * @param object $value
	 *
	 * @return void
	 */
	public function __set (string $key, object $value): void
	{
		$this->registry->set($key, $value);
	}

}
