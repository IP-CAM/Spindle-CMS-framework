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

class Csrf
{
	private string $token;
	private Session $session;

	/**
	 * CSRF constructor using custom session system (e.g., RedisHandler)
	 *
	 * @param Session $session
	 */
	public function __construct (Session $session)
	{
		$this->session = $session;

		if (empty($this->session->get('csrf_token'))) {
			$this->regenerateToken();
		} else {
			$this->token = $this->session->get('csrf_token');
		}
	}

	/**
	 * Get the current CSRF token
	 *
	 * @return string
	 */
	public function getCsrfToken (): string
	{
		return $this->token;
	}

	/**
	 * Validate a given CSRF token against the session token
	 *
	 * @param string $csrf
	 *
	 * @return bool
	 */
	public function checkToken (string $csrf): bool
	{
		return hash_equals($this->token, $csrf);
	}

	/**
	 * Regenerate the CSRF token
	 *
	 * Call this method after a successful form submission
	 *
	 * @return void
	 */
	public function regenerateToken (): void
	{
		$this->session->lazySet('csrf_token', spindle_token(32));
	}

}
