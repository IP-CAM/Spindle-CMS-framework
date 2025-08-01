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

namespace Spindle\System\Library\DB;

use stdClass;
use Exception;
use PDOException;
use PDO as NativePDO;

class Database
{
	private array $data = [];
	private int $affected = 0;

	/** @var array Static pool of connections, keyed by database configuration */
	private static $connectionPool = [];

	/** @var int Maximum number of connections per database configuration */
	private static $maxPoolSize = 3; // Adjust based on your needs

	/** @var NativePDO The PDO connection for this instance */
	private $connection;

	/** @var string Unique key for this database configuration */
	private $connectionKey;

	/**
	 * Constructor: Acquires a connection from the pool or creates a new one.
	 */
	public function __construct (
		string $hostname,
		string $username,
		string $password,
		string $database,
		string $port = '3306',
		bool   $persistent = TRUE
	)
	{
		// Generate a unique key for this database configuration
		$this->connectionKey = md5($hostname . $username . $password . $database . $port);

		// Check if there’s an available connection in the pool
		if (isset(self::$connectionPool[$this->connectionKey]) && !empty(self::$connectionPool[$this->connectionKey])) {
			// Reuse an existing connection from the pool
			$this->connection = array_pop(self::$connectionPool[$this->connectionKey]);
			error_log("[DB Pool] Reusing connection for key {$this->connectionKey}");
		} else {
			// Create a new connection if none are available
			try {
				$dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset=utf8mb4";
				$options = [
					NativePDO::ATTR_PERSISTENT => $persistent,
					NativePDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
				];
				$this->connection = new NativePDO($dsn, $username, $password, $options);
				error_log("[DB Pool] Creating NEW connection for key {$this->connectionKey}");
			} catch (PDOException $e) {
				throw new Exception("Error: Could not connect using {$username}@{$hostname}");
			}
		}

		// Set initial session configurations (example, adjust as needed)
		$this->connection->exec("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
		$this->connection->exec("SET FOREIGN_KEY_CHECKS = 0");
		$this->connection->exec("SET time_zone = '" . date('P') . "'");
	}

	/**
	 * Destructor: Returns the connection to the pool when the instance is destroyed.
	 */
	public function __destruct ()
	{
		if ($this->connection) {
			// Ensure the pool exists for this configuration
			if (!isset(self::$connectionPool[$this->connectionKey])) {
				self::$connectionPool[$this->connectionKey] = [];
			}

			// Return the connection to the pool if there’s space
			if (count(self::$connectionPool[$this->connectionKey]) < self::$maxPoolSize) {
				self::$connectionPool[$this->connectionKey][] = $this->connection;
			} else {
				// Pool is full, so close the connection
				$this->connection = NULL;
			}
		}
	}

	/**
	 * Manually release the connection back to the pool.
	 */
	public function releaseConnection ()
	{
		$this->__destruct();
	}

	/**
	 * Executes a SQL query.
	 *
	 * @param string $sql
	 *
	 * @return stdClass|true
	 */
	public function query (string $sql)
	{
		$sql = preg_replace('/(?:\'\:)([a-z0-9]*.)(?:\')/', ':$1', $sql);

		$statement = $this->connection->prepare($sql);

		try {
			if ($statement && $statement->execute($this->data)) {
				$this->data = [];

				if ($statement->columnCount()) {
					$data = $statement->fetchAll(NativePDO::FETCH_ASSOC);
					$statement->closeCursor();

					$result = new stdClass();
					$result->row = $data[0] ?? [];
					$result->rows = $data;
					$result->num_rows = count($data);
					$this->affected = 0;

					return $result;
				} else {
					$this->affected = $statement->rowCount();
					$statement->closeCursor();

					return TRUE;
				}
			} else {
				return TRUE;
			}
		} catch (PDOException $e) {
			throw new Exception('Error: ' . $e->getMessage() . ' <br/>Error Code : ' . $e->getCode() . ' <br/>' . $sql);
		}
	}

	public function queryWithBindings (string $sql, array $bindings): stdClass|bool
	{
		$statement = $this->connection->prepare($sql);

		try {
			foreach ($bindings as $key => $value) {
				$type = is_int($value) ? NativePDO::PARAM_INT : NativePDO::PARAM_STR;
				$statement->bindValue($key, $value, $type);
			}

			if ($statement->execute()) {
				if ($statement->columnCount()) {
					$data = $statement->fetchAll(NativePDO::FETCH_ASSOC);
					$statement->closeCursor();

					$result = new stdClass();
					$result->row = $data[0] ?? [];
					$result->rows = $data;
					$result->num_rows = count($data);
					$this->affected = 0;

					return $result;
				} else {
					$this->affected = $statement->rowCount();
					$statement->closeCursor();

					return TRUE;
				}
			}
		} catch (PDOException $e) {
			throw new Exception('Error: ' . $e->getMessage() . ' — ' . $sql);
		}

		return FALSE;
	}

	/**
	 * Escapes a value and binds it for query execution.
	 *
	 * @param string|int|float|bool $value
	 *
	 * @return string Placeholder key
	 */
	public function escape (string $value): string
	{
		$key = ':' . count($this->data);
		$this->data[$key] = $value;

		return $key;
	}

	public function countAffected (): int
	{
		return $this->affected;
	}

	public function getLastId (): ?int
	{
		$id = $this->connection->lastInsertId();

		return $id ? (int)$id : NULL;
	}

	public function isConnected (): bool
	{
		return $this->connection !== NULL;
	}

	public function beginTransaction (): void
	{
		$this->connection->beginTransaction();
	}

	public function commitTransaction (): void
	{
		if ($this->connection->inTransaction()) {
			$this->connection->commit();
		} else {
			$this->rollBackTransaction();
			throw new Exception("Cannot commit transaction. No transaction started.");
		}
	}

	public function rollBackTransaction (): void
	{
		if ($this->connection->inTransaction()) {
			$this->connection->rollBack();
		} else {
			throw new Exception("Cannot roll back transaction. No transaction started.");
		}
	}

	public function getRawConnection (): NativePDO
	{
		return $this->connection;
	}

	/**
	 * Returns the number of rows affected by the last non-SELECT query.
	 *
	 * @return int The number of rows affected (e.g., by UPDATE, DELETE, or INSERT).
	 */
	public function affectedRows (): int
	{
		return $this->affected;
	}

}
