<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection. Extends Kohana_Database_MySQL.
 * 
 * Modifications by Rick Jolly licensed under ISC http://www.opensource.org/licenses/isc-license.txt: 
 * - query() method ignores query $type parameter.
 * - If mysql result is not boolean, a resultset returned.
 * - Else if boolean and mysql_insert_id() is not 0, then
 * 		array(insert id, rows affected) returned.
 * - Else an integer rows affected is returned.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Database_MySQL extends Kohana_Database_MySQL {
	
	/**
	 * Note: $type is ignored. Reason being, $type is unnecessary for returning correct results, 
	 * and in some cases results will not correspond with $type - examples:
	 * "select * into outfile..."
	 * "insert into...on duplicate key update"
	 * Also, for insert queries on tables without an autoincrement id, there is no insert id to
	 * return.
	 * 
	 * @param string $type ignored
	 * @param string $sql
	 * @param boolean $as_object
	 * @return object mysql result
	 */
	public function query($type, $sql, $as_object)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling']))
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		if ( ! empty($this->_config['connection']['persistent']) AND $this->_config['connection']['database'] !== Database_MySQL::$_current_databases[$this->_connection_id])
		{
			// Select database on persistent connections
			$this->_select_db($this->_config['connection']['database']);
		}

		// Execute the query
		if (($result = mysql_query($sql, $this->_connection)) === FALSE)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			throw new Database_Exception(':error [ :query ]',
				array(':error' => mysql_error($this->_connection), ':query' => $sql),
				mysql_errno($this->_connection));
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if (! is_bool($result))
		{
			// Return an iterator of results
			return new Database_MySQL_Result($result, $sql, $as_object);
		}
		else
		{
			$insert_id = mysql_insert_id($this->_connection);
			if ($insert_id > 0)
			{
				return array(
					$insert_id,
					mysql_affected_rows($this->_connection),
				);
			}
			else
			{
				// Return the number of rows affected
				return mysql_affected_rows($this->_connection);
			}
		}
	}
	
	/**
	 * Start a SQL transaction
	 *
	 * @link http://dev.mysql.com/doc/refman/5.0/en/set-transaction.html
	 *
	 * @param   string  Isolation level
	 * @return  boolean
	 */
	public function begin($mode = NULL)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ($mode AND ! mysql_query("SET TRANSACTION ISOLATION LEVEL $mode", $this->_connection))
		{
			throw new Database_Exception(':error',
				array(':error' => mysql_error($this->_connection)),
				mysql_errno($this->_connection));
		}

		return (bool) mysql_query('START TRANSACTION', $this->_connection);
	}


	public function commit()
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return (bool) mysql_query('COMMIT', $this->_connection);
	}

	public function rollback()
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return (bool) mysql_query('ROLLBACK', $this->_connection);
	}

}