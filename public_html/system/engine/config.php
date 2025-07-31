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

class Config
{
	protected string $base;
	private array $data = [];

	public function __construct (string $base = '')
	{
		$this->base = rtrim($base, '/') . '/';
	}

	public function get (string $key): mixed
	{
		return $this->data[$key] ?? '';
	}

	public function set (string $key, mixed $value): void
	{
		$this->data[$key] = $value;
	}

	public function has (string $key): bool
	{
		return isset($this->data[$key]);
	}

	public function load (string $filename): array
	{
		$file = $this->base . $filename . '.php';

		if (is_file($file)) {
			$_ = [];
			require $file;
			$this->data = array_merge($this->data, $_);
		}

		return $this->data;
	}

}
