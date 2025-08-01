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

namespace Spindle\System\Library\Http;

use Spindle\System\Engine\Controller;

/**
 * Class URL
 */
class Url
{
	/**
	 * @var string
	 */
	private string $url;
	/**
	 * @var array<int, object>
	 */
	private array $rewrite = [];

	private $config;

	/**
	 * Constructor
	 *
	 * @param string $url
	 */
	public function __construct ($config)
	{
		$this->config = $config;
		$this->url = $this->config->get('site_url');
	}

	/**
	 * Add Rewrite
	 *
	 * Add a rewrite method to the URL system
	 *
	 * @param Controller $rewrite
	 *
	 * @return void
	 */
	public function addRewrite (object $rewrite): void
	{
		if (is_callable([$rewrite, 'rewrite'])) {
			$this->rewrite[] = $rewrite;
		}
	}

	/**
	 * Link
	 *
	 * Generates a URL
	 *
	 * @param string $route
	 * @param mixed  $args
	 * @param bool   $js
	 *
	 * @return string
	 */
	public function link (string $route, $args = '', bool $js = FALSE, ?string $target_application = ''): string
	{
		$base_url = $this->url;
		$protocol = parse_url($this->url, PHP_URL_SCHEME) ?? 'https';
		$host = parse_url($this->url, PHP_URL_HOST);

		// Determine root domain
		$root_domain = $this->getRootDomain($host);

		// Set base URL based on target_subdomain
		if ($target_application === NULL) {
			// No override — use whatever was injected/configured
			$base_url = $this->url;
		} else if ($target_application === 'app') {
			// “app” always goes to the public (www) site
			$base_url = $protocol . '://www.' . $root_domain . '/';
		} else if ($target_application === '') {
			if ($this->config->get('application') === 'app') {
				$base_url = $protocol . '://www.' . $root_domain . '/';
			} else {
				$base_url = $protocol . '://' . $this->config->get('subdomain') . '.' . $root_domain . '/';
			}
			$target_application = $this->config->get('subdomain');
		} else {
			// Blank string OR any other subdomain name
			// If you pass '' it will fall back to whatever your config/application says,
			// but you could also hard-code it if you have a specific subdomain in mind.
			$base_url = $protocol . '://' . $target_application . '.' . $root_domain . '/';
		}

		// Build route-based URL
		$url = $base_url . 'index.php?route=' . $route;

		if ($args) {
			$url .= '&' . (is_array($args) ? http_build_query($args) : trim($args, '&'));
		}

		// Inject language code if missing
		$lang_code = $this->config->get('config_language') ?? 'en-gb';
		if (!str_contains($url, '/' . $lang_code)) {
			$parsed = parse_url($url);
			$path = rtrim($parsed['path'] ?? '', '/');
			$url = str_replace($path, '/' . $lang_code . $path, $url);
		}

		// Perform SEO rewrite
		$original_url = $url;

		foreach ($this->rewrite as $rewrite) {
			$application = $target_application === '' ? 'app' : ($target_application ?? $this->config->get('application'));
			$rewritten = $rewrite->rewrite($url, $application);

			// If no rewrite occurred at all, or if keyword/path is missing
			if ($rewritten === $original_url || !preg_match('#/' . preg_quote($lang_code) . '/[^?]+#', $rewritten)) {
				$fallback_url = $protocol . '://www.' . $this->getRootDomain($host) . '/' . $lang_code . '/coming-soon';

				return $js ? $fallback_url : str_replace('&', '&amp;', $fallback_url);
			}

			$url = $rewritten;
		}

		$url = str_replace('%3F', '?', $url);

		return $js ? $url : str_replace('&', '&amp;', $url);
	}

	private function getRootDomain (string $host): string
	{
		// Handle www removal and subdomain parsing
		$parts = explode('.', $host);
		$count = count($parts);
		if ($count >= 2) {
			return $parts[$count - 2] . '.' . $parts[$count - 1];
		}

		return $host;
	}

	public function getRewrite (string $route): string
	{
		if (!isset($route)) {
			return "";
		}

		$link = $this->link($route);

		return str_replace($this->url, '', $link);

	}

}
