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

/**
 * Class Proxy
 *
 * @template TWraps of Model
 *
 * @mixin TWraps
 */
class Proxy
{
	/**
	 * @var array<string, object>
	 */
	protected array $data = [];

	/**
	 * __get
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function &__get (string $key)
	{
		if (isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			throw new Exception('Error: Could not call proxy key ' . $key . '!');
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
		$this->data[$key] = $value;
	}

	/**
	 * __isset
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset (string $key): bool
	{
		return isset($this->data[$key]);
	}

	/**
	 * __unset
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function __unset (string $key): void
	{
		unset($this->data[$key]);
	}

	/**
	 * __call
	 *
	 * @param string               $method
	 * @param array<string, mixed> $args
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __call (string $method, array $args)
	{
		// Hack for pass-by-reference
		foreach ($args as $key => &$value) ;

		if (isset($this->data[$method])) {
			return ($this->data[$method])(...$args);
		} else {
			$trace = debug_backtrace();

			throw new Exception('<b>Notice</b>:  Undefined property: Proxy::' . $method . ' in <b>' . $trace[0]['file'] . '</b> on line <b>' . $trace[0]['line'] . '</b>');
		}
	}

}
