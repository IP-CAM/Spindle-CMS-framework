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

namespace Spindle\System\Library\Loggers;

/**
 * Class Log
 */
class Log
{
	/**
	 * @var string
	 */
	private string $file;

	/**
	 * Constructor
	 *
	 * @param string $filename
	 */
	public function __construct (string $filename)
	{
		$this->file = DIR_LOGS . $filename;
		if (!is_file($this->file)) {
			$handle = fopen($this->file, 'w');

			fclose($handle);
		}
	}

	/**
	 * Write
	 *
	 * @param mixed $message
	 *
	 * @return void
	 */
	public function write ($message): void
	{
		file_put_contents($this->file, date('Y-m-d H:i:s') . ' - ' . print_r($message, TRUE) . "\n", FILE_APPEND);
	}

	/**
	 * Clear log file
	 *
	 * @return void
	 */
	public function clearLog (): void
	{
		file_put_contents($this->file, '');
		file_put_contents(DIR_LOGS . 'trace.log', '');
	}

}
