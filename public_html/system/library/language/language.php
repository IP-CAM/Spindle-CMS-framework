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

namespace Spindle\System\Library\Language;

/**
 * Class Language
 */
class Language
{
	/**
	 * @var string
	 */
	protected string $code;
	/**
	 * @var string
	 */
	protected string $directory;
	/**
	 * @var array<string, string>
	 */
	protected array $path = [];
	/**
	 * @var array<string, string>
	 */
	protected array $data = [];
	/**
	 * @var array<string, array<string, array<string, mixed>>>
	 */
	protected array $cache = [];

	/**
	 * @var array<string, string>
	 */
	protected array $replacements;

	/**
	 * Constructor
	 *
	 * @param string $code
	 */
	public function __construct (string $code)
	{
		$this->code = $code;
		$this->replacements = [];
	}

	/**
	 * Add Path
	 *
	 * @param string $namespace
	 * @param string $directory
	 *
	 * @return void
	 */
	public function addPath (string $namespace, string $directory = ''): void
	{
		if (!$directory) {
			$this->directory = $namespace;
		} else {
			$this->path[$namespace] = $directory;
		}
	}

	protected function autoReplace (string $text): string
	{
		return strtr($text, $this->replacements);
	}

	/**
	 *
	 * Set language text string
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 */
	public function set (string $key, string $value): void
	{
		$this->data[$key] = $value;
		$this->data[$key] = $this->autoReplace($value);
	}

	/**
	 * All
	 *
	 * @param string $prefix
	 *
	 * @return array<string, string>
	 */
	public function all (string $prefix = ''): array
	{
		if (!$prefix) {
			return $this->data;
		}

		$_ = [];

		$length = strlen($prefix);

		foreach ($this->data as $key => $value) {
			if (substr($key, 0, $length) == $prefix) {
				$_[substr($key, $length + 1)] = $value;
			}
		}

		return $_;
	}

	/**
	 * Clear
	 *
	 * @return void
	 */
	public function clear (): void
	{
		$this->data = [];
	}

	/**
	 * Load
	 *
	 * @param string $filename
	 * @param string $prefix
	 * @param string $code Language code
	 *
	 * @return array<string, string>
	 */
	public function load (string $filename, string $prefix = '', string $code = ''): array
	{
		if (!$code) {
			$code = $this->code;
		}

		if (!isset($this->cache[$code][$filename])) {
			$_ = [];

			// Load selected language file to overwrite the default language keys
			$file = $this->directory . $code . '/' . $filename . '.php';

			$namespace = '';

			$parts = explode('/', $filename);

			foreach ($parts as $part) {
				if (!$namespace) {
					$namespace .= $part;
				} else {
					$namespace .= '/' . $part;
				}

				if (isset($this->path[$namespace])) {
					$file = $this->path[$namespace] . $code . substr($filename, strlen($namespace)) . '.php';
				}
			}

			if (is_file($file)) {
				require($file);
			}

			$this->cache[$code][$filename] = $_;
		} else {
			$_ = $this->cache[$code][$filename];
		}

		if ($prefix) {
			foreach ($_ as $key => $value) {
				$_[$prefix . '_' . $key] = $value;

				unset($_[$key]);
			}
		}

		foreach ($_ as $key => $val) {
			$_[$key] = $this->autoReplace($val);
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}

	/**
	 * Get
	 *
	 * Get language text string
	 *
	 * @param string $key
	 */
	public function get (string $key): string
	{
		return $this->data[$key] ?? $key;
	}

	/**
	 * formatText
	 *
	 * Formats a text string by replacing placeholders with provided arguments.
	 *
	 * @param string $text    The text string to format.
	 * @param mixed  ...$args The arguments to replace the placeholders with.
	 *
	 * @return string The formatted text string.
	 */
	public function formatText (string $text, ...$args): string
	{
		return vsprintf($text, $args);
	}

}
