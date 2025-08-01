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

namespace Spindle\App\Controller\Common;

use Spindle\System\Engine\Controller;

class Home extends Controller
{

	public function index ()
	{
		$data['testing'] = 'This is the homepage';

		$this->response->setOutput($this->load->view('common/home', $data));
	}

}