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

namespace Spindle\Shared\Controller\Error;

use Spindle\System\Engine\Controller;

class NotFound extends Controller
{

	public function index (): void
	{
		// Set the header to a 404
		$this->response->addHeader('HTTP/1.1 404 Not Found');

		// Create the home link
		$data['home_link'] = $this->url->link('common/home', target_application: 'app');

		$this->response->setOutput($this->load->view('shared/error/not_found', $data));
	}

}