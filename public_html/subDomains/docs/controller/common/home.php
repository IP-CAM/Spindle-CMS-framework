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

namespace Spindle\Docs\Controller\Common;

use Spindle\System\Engine\Controller;

class Home extends Controller
{

	public function index ()
	{
		// Load in the extra css page (duplicates code from home page, but so what - i copied and pasted)
		$this->document->addStyle(CDN_LOCATION . "css/docs.css", sortOrder: 100);

		$data['header'] = $this->load->controller('common/header');
		$data['content_top'] = $this->load->controller('common/content_top');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

}