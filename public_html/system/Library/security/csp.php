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

use Exception;

/**
 * Class CrossSiteProtection
 * Handles generation of nonces for Content Security Policy (CSP).
 */
class Csp
{
	/**
	 * @var string|null The generated nonce for the current request.
	 */
	private ?string $nonce = NULL;

	/**
	 * Constructor.
	 * Generates the nonce immediately upon instantiation.
	 */
	public function __construct ()
	{
		$this->generateNonce();
	}

	/**
	 * Generates a cryptographically secure nonce.
	 * Uses random_bytes for security and base64_encode for a standard format.
	 *
	 * @return string The generated nonce.
	 */
	public function generateNonce (): string
	{
		// Generate 16 bytes of random data (adjust length if needed)
		// base64 encoding is common for nonces
		$this->nonce = base64_encode(random_bytes(16));

		return $this->nonce;
	}

	/**
	 * Gets the currently stored nonce.
	 * Ensures a nonce exists if called before generateNonce (though the constructor handles this).
	 *
	 * @return string The nonce.
	 * @throws Exception If nonce generation fails (highly unlikely with random_bytes).
	 */
	public function getNonce (): string
	{
		if ($this->nonce === NULL) {
			// Should ideally not happen due to constructor call, but as a fallback
			$this->generateNonce();
		}

		return $this->nonce;
	}

}