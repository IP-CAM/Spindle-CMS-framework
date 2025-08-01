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

namespace Spindle\System\Library\Cache;

class FileCache
{
	private int $expire;

	public function __construct (int $expire = 3600)
	{
		$this->expire = $expire;
	}

	public function get (string $key): array
	{
		$path = $this->resolvePath($key);
		$pattern = $path . '.*';
		$files = glob($pattern);

		foreach ($files as $file) {
			$time = (int)substr(strrchr($file, '.'), 1);

			if ($time < time()) {
				@unlink($file) || clearstatcache(FALSE, $file);
			} else {
				return json_decode(file_get_contents($file), TRUE) ?? [];
			}
		}

		return [];
	}

	public function set (string $key, mixed $value, int $expire = 0): void
	{
		$this->delete($key);

		$ttl = $expire > 0 ? $expire : $this->expire;
		$filename = $this->resolvePath($key) . '.' . (time() + $ttl);

		// Ensure directory exists
		$dir = dirname($filename);
		if (!is_dir($dir)) {
			mkdir($dir, 0777, TRUE);
		}

		file_put_contents($filename, json_encode($value));
	}

	public function delete (string $key): void
	{
		$pattern = $this->resolvePath($key) . '.*';
		$files = glob($pattern);

		foreach ($files as $file) {
			@unlink($file) || clearstatcache(FALSE, $file);
		}
	}

	public function cleanExpired (): void
	{
		$files = glob(DIR_CACHE . '**/cache.*', GLOB_BRACE);

		foreach ($files as $file) {
			$time = (int)substr(strrchr($file, '.'), 1);

			if ($time < time()) {
				@unlink($file) || clearstatcache(FALSE, $file);
			}
		}
	}

	public function maybeClean (): void
	{
		if (mt_rand(1, 100) === 1) {
			$this->cleanExpired();
		}
	}

	private function sanitize (string $key): string
	{
		return preg_replace('/[^A-Z0-9\._\/-]/i', '', $key);
	}

	private function resolvePath (string $key): string
	{
		$key = $this->sanitize($key);
		$segments = explode('/', $key);
		$filename = array_pop($segments);

		$path = DIR_CACHE;
		if (!empty($segments)) {
			$path .= implode('/', $segments) . '/';
		}

		return $path . 'cache.' . $filename;
	}

	public function __destruct ()
	{
		$this->maybeClean();
	}

}
