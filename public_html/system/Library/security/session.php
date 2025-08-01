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

namespace Spindle\System\Library\Security;

use Redis as RedisClient;

class Session
{
	protected int $timeout = 1800; // 30 minutes
	protected int $lazy_timeout = 2592000; // 30 days
	protected string $user_id = '';
	protected string $session_id = '';
	protected string $lazy_session_id = '';
	protected string $fingerprint = '';
	protected string $prefix = 'sess:';
	protected string $lazy_prefix = 'lazy:';

	protected RedisClient $redis;
	protected array $data = [];
	protected array $lazyData = [];
	protected $registry;
	protected $config;

	public function __construct ($registry, int $timeout = 1800)
	{
		$this->timeout = $timeout;
		$this->fingerprint = $this->generateFingerprint();
		$this->registry = $registry;
		$this->config = $registry->get('config');

		$this->redis = new RedisClient();
		$this->redis->connect('127.0.0.1', 6379);

		// Init lazy session cookie
		if (!isset($_COOKIE['spindle_lazy']) || !preg_match('/^[a-f0-9]{32,64}$/', $_COOKIE['spindle_lazy'])) {
			$this->lazy_session_id = bin2hex(random_bytes(16));
			$this->createLazySession();
		} else {
			$this->lazy_session_id = $_COOKIE['spindle_lazy'];
		}
	}

	protected function createLazySession ()
	{
		setcookie(
			'spindle_lazy',
			$this->lazy_session_id,
			[
				'expires' => time() + $this->lazy_timeout,
				'path' => '/',
				'domain' => $this->config->get('session_domain'), // allows use across all subdomains
				'secure' => TRUE,
				'httponly' => TRUE,
				'samesite' => 'Strict', // or 'None' if used in cross-origin fetch()
			]
		);
	}

	public function start (): void
	{
		session_start();
		$this->session_id = session_id();
		$session = $this->read($this->session_id);

		if (!$session || $this->isExpired($session) || !$this->isValid($session)) {
			$this->destroy($this->session_id);
			session_regenerate_id(TRUE);
			$this->session_id = session_id();

			$this->write($this->session_id, [
				'user_id' => '',
				'data' => [],
				'last_activity' => time(),
				'fingerprint' => $this->fingerprint,
			]);
		}

		$this->syncFromRedis();
	}

	public function setUrlToken (string $tokenName): string
	{
		// Always generate a new token and set it, overwriting any existing one.
		// This ensures the token is fresh for each request that calls setUrlToken.
		$token = spindle_token(32);
		$this->lazySet($tokenName, $token);

		return $token;
	}

	public function getSessionId (): string
	{
		return $this->session_id;
	}

	public function getLazySessionId (): string
	{
		return $this->lazy_session_id;
	}

	public function getUserId (): string
	{
		return $this->user_id;
	}

	public function get (string $key)
	{
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}

		$lazyData = $this->readLazySession();

		return $lazyData[$key] ?? NULL;
	}

	public function set (string $key, $value): void
	{
		$this->data[$key] = $value;
		$this->syncToRedis();
	}

	public function lazySet (string $key, $value): void
	{
		$lazyData = $this->readLazySession();
		$lazyData[$key] = $value;
		$this->writeLazySession($lazyData);
	}

	public function has (string $key): bool
	{
		return isset($this->data[$key]) || $this->lazyHas($key);
	}

	public function lazyHas (string $key): bool
	{
		$lazyData = $this->readLazySession();

		return isset($lazyData[$key]);
	}

	public function delete (string $key): void
	{
		unset($this->data[$key]);

		$lazyData = $this->readLazySession();
		unset($lazyData[$key]);
		$this->writeLazySession($lazyData);

		$this->syncToRedis();
	}

	public function destroy (string $session_id): bool
	{
		$session = $this->read($session_id);
		if (!empty($session['user_id'])) {
			$this->redis->del($this->prefix . 'user:' . $session['user_id']);
		}

		return $this->redis->del($this->prefix . $session_id) > 0;
	}

	public function destroyLazySession (): void
	{
		$this->redis->del($this->lazy_prefix . $this->lazy_session_id);
		$this->createLazySession();
	}

	public function destroyAll (): void
	{
		// 1. Delete the main session data from Redis
		if (!empty($this->session_id)) {
			$this->redis->del($this->prefix . $this->session_id);
		}

		// 2. Delete known lazy session keys (only those set during this session)
		foreach (array_keys($this->data) as $key) {
			$this->redis->del($this->lazy_prefix . $key);
		}

		// 3. Delete any user-to-session binding
		if (!empty($this->user_id)) {
			$this->redis->del($this->prefix . 'user:' . $this->user_id);
		}

		// 4. Reset internal memory
		$this->data = [];
		$this->user_id = '';

		// 5. Regenerate PHP session ID if session is active
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_regenerate_id(TRUE);
			$this->session_id = session_id();
		}

		// 6. Write a fresh, clean session to Redis
		$this->write($this->session_id, [
			'user_id' => '',
			'data' => [],
			'last_activity' => time(),
			'fingerprint' => $this->fingerprint,
		]);
	}

	protected function read (string $session_id): ?array
	{
		$raw = $this->redis->get($this->prefix . $session_id);

		return $raw ? json_decode($raw, TRUE) : NULL;
	}

	protected function readLazySession (): array
	{
		$raw = $this->redis->get($this->lazy_prefix . $this->lazy_session_id);

		return $raw ? json_decode($raw, TRUE) : [];
	}

	protected function writeLazySession (array $data): void
	{
		$this->redis->setex($this->lazy_prefix . $this->lazy_session_id, $this->lazy_timeout, json_encode($data));
	}

	protected function write (string $session_id, array $data): bool
	{
		if (!empty($data['user_id'])) {
			$this->invalidateOtherSessions($data['user_id']);
			$this->redis->set($this->prefix . 'user:' . $data['user_id'], $session_id, $this->timeout);
		}

		return $this->redis->setex(
			$this->prefix . $session_id,
			$this->timeout,
			json_encode([
				'user_id' => $data['user_id'],
				'data' => $data['data'],
				'last_activity' => time(),
				'fingerprint' => $this->fingerprint,
			])
		);
	}

	protected function syncFromRedis (): void
	{
		$session = $this->read($this->session_id);
		$this->data = $session['data'] ?? [];
		$this->user_id = $session['user_id'] ?? '';
	}

	protected function syncToRedis (): void
	{
		$session = $this->read($this->session_id) ?? [
			'user_id' => '',
			'data' => [],
			'last_activity' => time(),
			'fingerprint' => $this->fingerprint,
		];

		$session['data'] = $this->data;
		$session['last_activity'] = time();

		$this->write($this->session_id, $session);
	}

	public function persist (): void
	{
		$this->syncToRedis();
	}

	public function updateSessionId (string $new_id): void
	{
		$this->session_id = $new_id;
	}

	protected function invalidateOtherSessions (string $user_id): void
	{
		$existing = $this->redis->get($this->prefix . 'user:' . $user_id);
		if ($existing && $existing !== $this->session_id) {
			$this->destroy($existing);
		}
	}

	protected function isExpired (array $session): bool
	{
		return (time() - $session['last_activity']) > $this->timeout;
	}

	protected function isValid (array $session): bool
	{
		return $session['fingerprint'] === $this->generateFingerprint();
	}

	protected function generateFingerprint (): string
	{
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown-agent';
		$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

		return sha1($userAgent . $ipAddress);
	}

	public function unset (string $key): void
	{
		$this->delete($key);
	}

	// --- Magic Accessors ----------------------

	public function __get (string $key)
	{
		return $this->get($key);
	}

	public function __set (string $key, $value): void
	{
		$this->set($key, $value);
	}

	public function __isset (string $key): bool
	{
		return $this->has($key);
	}

	public function __unset (string $key): void
	{
		$this->delete($key);
	}

}
