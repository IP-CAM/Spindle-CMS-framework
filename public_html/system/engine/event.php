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

class Event
{
	/**
	 * @var Registry
	 */
	protected Registry $registry;
	/**
	 * @var array<int, array<string, mixed>>
	 */
	protected array $data = [];

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
	 * Register
	 *
	 * @param string $trigger
	 * @param Action $action
	 * @param int    $priority
	 *
	 * @return void
	 */
	public function register (string $trigger, Action $action, int $priority = 0): void
	{
		$this->data[] = [
			'trigger' => $trigger,
			'action' => $action,
			'priority' => $priority,
		];

		$sort_order = [];

		foreach ($this->data as $key => $value) {
			$sort_order[$key] = $value['priority'];
		}

		array_multisort($sort_order, SORT_ASC, $this->data);
	}

	/**
	 * Trigger
	 *
	 * @param string       $event
	 * @param array<mixed> $args
	 *
	 * @return mixed
	 */
	public function trigger (string $event, array $args = [])
	{
		foreach ($this->data as $value) {
			if (preg_match('/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($value['trigger'], '/')) . '/', $event)) {
				$value['action']->execute($this->registry, $args);
			}
		}

		return '';
	}

	/**
	 * Unregister
	 *
	 * @param string $trigger
	 * @param string $route
	 *
	 * @return void
	 */
	public function unregister (string $trigger, string $route): void
	{
		foreach ($this->data as $key => $value) {
			if ($trigger == $value['trigger'] && $value['action']->getId() == $route) {
				unset($this->data[$key]);
			}
		}
	}

	/**
	 * Clear
	 *
	 * @param string $trigger
	 *
	 * @return void
	 */
	public function clear (string $trigger): void
	{
		foreach ($this->data as $key => $value) {
			if ($trigger == $value['trigger']) {
				unset($this->data[$key]);
			}
		}
	}

}
