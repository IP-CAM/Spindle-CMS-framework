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
use Spindle\System\Engine\Controller;

/**
 * Class Action
 *
 * Allows the stored action to be passed around and be executed by the framework and events.
 *
 */
class Action
{
	/**
	 * @var string
	 */
	private string $route;

	/**
	 * @var string
	 */
	private string $controller;

	/**
	 * @var string
	 */
	private string $method;

	/**
	 * Constructor
	 *
	 * @param string $route
	 */
	public function __construct (string $route)
	{
		$this->route = preg_replace('/[^a-zA-Z0-9_|\/\.]/', '', $route);

		$pos = strrpos($route, '.');

		if ($pos !== FALSE) {
			$this->controller = substr($route, 0, $pos);
			$this->method = substr($route, $pos + 1);
		} else {
			$this->controller = $route;
			$this->method = 'index';
		}
	}

	/**
	 * Get Id
	 *
	 * @return string
	 */
	public function getId (): string
	{
		return $this->route;
	}

	/**
	 * Execute
	 *
	 * @param Registry     $registry
	 * @param array<mixed> $args
	 *
	 * @return mixed
	 */
	public function execute (Registry $registry, array &$args = [])
	{
		// Stop any magical methods being called
		if (substr($this->method, 0, 2) == '__') {
			return new Exception('Error: Calls to magic methods are not allowed!');
		}

		// Create a new key to store the model object
		$key = 'fallback_controller_' . str_replace('/', '_', $this->controller);

		if (!$registry->has($key)) {
			$object = $registry->get('factory')->controller($this->controller);
		} else {
			$object = $registry->get($key);
		}

		if ($object instanceof Controller) {
			$registry->set($key, $object);
		} else {
			// If action cannot be executed, we return an error object.
			return new Exception('Error: Could not load controller ' . $this->route . '!');
		}

		$callable = [$object, $this->method];

		if (is_callable($callable)) {
			return $callable(...$args);
		} else {
			return new Exception('Error: Could not call controller ' . $this->route . '!');
		}
	}

}
