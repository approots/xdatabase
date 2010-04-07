<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PDO database connection. Extends Kohana_Database_PDO
 * 
 * Modifications by Rick Jolly: 
 * - query method ignores query $type parameter.
 * - If PDO result has columns (meaning has resultset), 
 * 		a resultset is returned.
 * - Else if no resultset and insert id is not 0, then
 * 		array(insert id, rows affected) returned.
 * - Else an integer rows affected is returned.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Database_PDO extends Kohana_Database_PDO {
	
	/**
	 * Note: $type is ignored. Reason being, $type is unnecessary for returning correct results, 
	 * and in some cases results will not correspond with $type - examples:
	 * "select * into outfile..."
	 * "insert into...on duplicate key update"
	 * Also, for insert queries on tables without an autoincrement id, only rows affected 
	 * need be returned.
	 * 
	 * @param string $type ignored
	 * @param string $sql
	 * @param boolean $as_object
	 * @return object PDO result
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

		try
		{
			$result = $this->_connection->query($sql);
		}
		catch (Exception $e)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			// Rethrow the exception
			throw $e;
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($result->columnCount() > 0)
		{
			// Convert the result into an array, as PDOStatement::rowCount is not reliable
			if ($as_object === FALSE)
			{
				$result->setFetchMode(PDO::FETCH_ASSOC);
			}
			elseif (is_string($as_object))
			{
				$result->setFetchMode(PDO::FETCH_CLASS, $as_object);
			}
			else
			{
				$result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
			}
			
			$result = $result->fetchAll();

			// Return an iterator of results
			return new Database_Result_Cached($result, $sql, $as_object);
		}
		else
		{
			$insert_id = $this->_connection->lastInsertId();
			if ($insert_id > 0)
			{
				return array (
					$insert_id,
					$result->rowCount(),
				);
			}
			else
			{
				// Return the number of rows affected
				return $result->rowCount();
			}
		}
	}
} // End Database_PDO