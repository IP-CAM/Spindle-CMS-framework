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

namespace Spindle\System\Library\Template;

use Exception;
use Twig\Environment;
use Twig\Error\Error as TwigError;
use Twig\Extension\DebugExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class Template
{
	protected string $root;
	protected FilesystemLoader $loader;
	protected Environment $twig;
	protected array $paths = [];
	protected string $defaultPath = '';
	protected $session;

	public function __construct ($session)
	{
		$this->session = $session;

		$this->root = rtrim(MAIN_WEB_ROOT, '/'); // no trailing slash
		$this->loader = new FilesystemLoader('./', $this->root);

		$this->twig = new Environment($this->loader, [
			'charset' => 'utf-8',
			'autoescape' => FALSE,
			'debug' => TRUE,
			'auto_reload' => TRUE,
			'cache' => DIR_CACHE . 'template/',
		]);

		$this->twig->addExtension(new DebugExtension());

		// Inject globals
		$token = $this->session->get('csrf_token');
		if (!$token) {
			$token = bin2hex(random_bytes(16));
			$this->session->lazySet('csrf_token', $token);
		}

		$this->twig->addGlobal('HONEYPOT', '<div class="honeypot-field"><label for="input-telephone">Leave this field blank</label><input type="text" name="phone_number" id="input-telephone" autocomplete="off" tabindex="-1" /></div>');
		$this->twig->addGlobal('CSRF', '<input type="hidden" class="csrf_token" name="csrf_token" value="' . $token . '">');
	}

	public function addPath (string $namespace, string $directory = ''): void
	{
		if ($directory === '') {
			$this->defaultPath = $namespace;
		} else {
			$this->paths[$namespace] = $directory;
		}
	}

	public function render (string $filename, array $data = [], string $code = ''): string
	{
		try {
			$file = $this->resolveTemplatePath($filename);

			if ($code) {
				$loader = new ArrayLoader([$file => $code]);
				$env = new Environment($loader, [
					'autoescape' => FALSE,
					'debug' => TRUE,
				]);
				$env->addExtension(new DebugExtension());

				return $env->render($file, $data);
			}

			return $this->twig->render($file, $data);
		} catch (TwigError $e) {
			throw new Exception('Template error in "' . $filename . '": ' . $e->getMessage());
		}
	}

	private function resolveTemplatePath (string $filename): string
	{
		$file = $this->defaultPath . $filename . '.twig';
		$namespace = '';
		$parts = explode('/', $filename);

		foreach ($parts as $part) {
			$namespace .= ($namespace ? '/' : '') . $part;

			if (isset($this->paths[$namespace])) {
				$file = $this->paths[$namespace] . substr($filename, strlen($namespace) + 1) . '.twig';
				break;
			}
		}

		return ltrim(substr($file, strlen($this->root)), '/');
	}

}
