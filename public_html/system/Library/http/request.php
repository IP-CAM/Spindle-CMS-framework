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

namespace Spindle\system\library\http;

/**
 * Secure Request Handler
 */
class Request
{
	public array $get = [];
	public array $post = [];
	public array $cookie = [];
	public array $files = [];
	public array $server = [];

	public function __construct ()
	{
		$this->get = $this->clean($_GET);
		$this->post = $this->clean($_POST);
		$this->cookie = $this->clean($_COOKIE);
		$this->files = $this->clean($_FILES);
		$this->server = $this->clean($_SERVER);
	}

	// -- Accessors --------------------------------------------------

	public function get (string $key, string $type = ''): mixed
	{
		return $this->filter($this->get[$key] ?? NULL, $type);
	}

	public function post (string $key, string $type = ''): mixed
	{
		return $this->filter($this->post[$key] ?? NULL, $type);
	}

	public function cookie (string $key, string $type = ''): mixed
	{
		return $this->filter($this->cookie[$key] ?? NULL, $type);
	}

	public function file (string $key): mixed
	{
		return $this->files[$key] ?? NULL;
	}

	public function server (string $key, string $type = ''): mixed
	{
		return $this->filter($this->server[$key] ?? NULL, $type);
	}

	// -- Method + JSON Helpers --------------------------------------

	public function method (): string
	{
		return $_SERVER['REQUEST_METHOD'] ?? 'GET';
	}

	public function isPost (): bool
	{
		return $this->method() === 'POST';
	}

	public function isGet (): bool
	{
		return $this->method() === 'GET';
	}

	public function json (): array
	{
		/**
		 * static $cache explanation:
		 * This prevents parsing the same input stream (php://input) multiple times.
		 * It's evaluated only once per request and stored for reuse.
		 */
		static $cache = NULL;

		if ($cache !== NULL) return $cache;

		$raw = file_get_contents('php://input');
		$decoded = json_decode($raw, TRUE);

		$cache = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
			? $this->clean($decoded)
			: [];

		return $cache;
	}

	// -- Sanitization & Filtering -----------------------------------

	private function filter (mixed $value, string $type): mixed
	{
		return match ($type) {
			'string' => is_string($value) ? trim($value) : '',
			'int' => filter_var($value, FILTER_VALIDATE_INT),
			'float' => filter_var($value, FILTER_VALIDATE_FLOAT),
			'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
			'array' => is_array($value) ? $value : [],
			default => $value,
		};
	}

	/**
	 * Sanitize array keys and optionally trim string values.
	 */
	private function clean ($data): mixed
	{
		if (is_array($data)) {
			$clean = [];
			foreach ($data as $key => $value) {
				/**
				 * Explanation for:
				 * $cleanKey = is_string($key) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $key) : $key;
				 * This ensures no special characters in keys — useful for security.
				 * Prevents header injection, malformed keys, or exploits like `__proto__`.
				 */
				$cleanKey = is_string($key) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $key) : $key;
				$clean[$cleanKey] = $this->clean($value);
			}

			return $clean;
		}

		return is_string($data) ? trim($data) : $data;
	}

	// -- Raw Access -------------------------------------------------

	public function all (): array
	{
		return array_merge($this->get, $this->post);
	}

	public function raw (): string
	{
		return file_get_contents('php://input');
	}

	// Optional: expose the full original arrays if needed
	public function getData (): array
	{
		return $this->get;
	}

	public function postData (): array
	{
		return $this->post;
	}

	public function cookieData (): array
	{
		return $this->cookie;
	}

	public function fileData (): array
	{
		return $this->files;
	}

	public function serverData (): array
	{
		return $this->server;
	}

}
