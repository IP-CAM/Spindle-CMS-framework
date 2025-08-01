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

class ContentTop extends Controller
{

	public function index (): string
	{
		$data['link_home'] = $this->url->link('common/home', target_application: 'app');
		$data['link_docs'] = $this->url->link('common/home', target_application: 'docs');

		// return the view
		return $this->load->view('shared/common/content_top', $data);
	}

}