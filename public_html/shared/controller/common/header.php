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

namespace Spindle\Shared\Controller\Common;

use Spindle\System\Engine\Controller;

class Header extends Controller
{

	public function index (): string
	{
		// get the nonce for the Cross site Protection
		$nonce = $this->csp->getNonce();
		$data['nonce'] = $nonce;

		// Set Canonical link
		$this->document->setCanonical(); // This will trigger the fallback to generate from $_SERVER if empty

		// Set the stylesheets
		$this->document->addStyle(CDN_LOCATION . "css/main.css");

		// === Meta Fallbacks: auto-fill if not set ===
		if (empty($this->document->getTitle()) || $this->document->getTitle() === DEFAULT_TITLE) {
			$this->document->setTitle('Spindle CMS | Lightweight Developer-Centric Content Framework');
		}

		if (empty($this->document->getDescription()) || $this->document->getDescription() === DEFAULT_DESCRIPTION) {
			$this->document->setDescription("Spindle is a streamlined, open-source CMS framework for PHP developers. Built from OpenCart's core structure and reimagined for modern content-driven websites, without the bloat.");
		}

		if (empty($this->document->getKeywords()) || $this->document->getKeywords() === DEFAULT_KEYWORDS) {
			$this->document->setKeywords("Spindle CMS, PHP CMS Framework, OpenCart Fork, Lightweight CMS, Developer CMS, Modular PHP Framework, MVC CMS, GPLv3 CMS, Self-Hosted Content Platform");
		}

		// Auto-fill Open Graph
		if (empty($this->document->getOgTitle())) {
			$this->document->setOgTitle($this->document->getTitle());
		}

		if (empty($this->document->getOgDescription())) {
			$this->document->setOgDescription($this->document->getDescription());
		}

		if (empty($this->document->getOgUrl())) {
			$this->document->setOgUrl($this->document->getCanonical());
		}

		// Auto-fill Twitter
		if (empty($this->document->getTwitterTitle())) {
			$this->document->setTwitterTitle($this->document->getTitle());
		}

		if (empty($this->document->getTwitterDescription())) {
			$this->document->setTwitterDescription($this->document->getDescription());
		}

		if (empty($this->document->getTwitterImage())) {
			$this->document->setTwitterImage($this->document->getOgImage());
		}

		// Render the header information
		$data['header_info'] = $this->document->renderHead();

		// return the view
		return $this->load->view('shared/common/header', $data);
	}

}