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

namespace Spindle\shared\controller\system;

use Spindle\System\Engine\Controller;

class Application extends Controller
{

	public function index (): void
	{
		// Load the helpers
		$this->load->helper('helpers');

		// Load other modals, functions and things in here that are application specific

	}

}