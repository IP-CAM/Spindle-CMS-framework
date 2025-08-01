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
 * Class Factory
 */
class Factory
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
	 * Controller
	 *
	 * @param string $route
	 *
	 * @return Controller
	 */
	public function controller (string $route): object
	{
		// Sanitize the route
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', $route);

		// Build primary application class
		$class = 'Spindle\\' . ucfirst($this->registry->get('config')->get('application')) . '\Controller\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));

		// Shared fallback class
		$shared = 'Spindle\\Shared\\Controller\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));

		if (class_exists($class)) {
			return new $class($this->registry);
		}

		if (class_exists($shared)) {
			return new $shared($this->registry);
		}

		return new Action('error/not_found');
	}

	/**
	 * @throws Exception
	 */
	public function model (string $route): object
	{
		// Sanitize the route
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', $route);

		// Build primary application class
		$class = 'Spindle\\' . ucfirst($this->registry->get('config')->get('application')) . '\Model\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));

		// Shared fallback class
		$shared = 'Spindle\\Shared\\Model\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));

		error_log("----------------------");
		error_log($class);
		error_log("----------------------");

		if (class_exists($class)) {
			return new $class($this->registry);
		}

		if (class_exists($shared)) {
			return new $shared($this->registry);
		}

		throw new Exception("Error: Could not load model '$route'!");
	}

	/**
	 * Library
	 *
	 * @param string       $route
	 * @param array<mixed> $args
	 *
	 * @return object
	 * @throws Exception
	 */
	public function library (string $route, array $args): object
	{
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', $route);

		// Generate the class
		$class = 'Spindle\System\Library\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));

		// Check if the requested model is already stored in the registry.
		if (class_exists($class)) {
			return new $class(...$args);
		} else {
			throw new Exception('Error: Could not load library ' . $route . '!');
		}
	}

}
