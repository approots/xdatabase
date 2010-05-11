<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query object creation helper methods. Extends Kohana_DB.
 * 
 * Added method sql($sql) as an alias to DB:query($any_type, $sql)
 *
 * @package Database
 * @author Rick Jolly <rickjolly@hotmail.com>
 * @copyright (c) 2010 Rick Jolly
 * @license ISC http://www.opensource.org/licenses/isc-license.txt
 */
class Query extends Kohana_DB {
	
	/**
	 * Alias to DB:query($any_type, $sql). The Database type is not necessary
	 * and ignored in this extended database module.
	 * 
	 * @param string $sql
	 * @return object Database_Query 
	 */
	public static function sql($sql)
	{	
		return new Database_Query(NULL, $sql);
	}
	
	/**
	 * Start a SQL transaction
	 *
	 * Some examples of different ways to use transactions:
	 * 
	 * 
	 * 
	 * // 1) Example using the xdatabase Query class
	 * try
	 * {
	 *   Query::begin();
	 *   Query::sql($sql)->execute();
	 *   // ... more code/queries
	 *   Query::commit();
	 * }
	 * catch (Exception $e)
	 * {
	 *   Query::rollback();
	 * }
	 * 
	 * // 2) Example using existing Kohana style
	 * try
	 * {
	 * 	 $db = Database::instance();
	 *   $db->begin();
	 *   DB::query(Database::INSERT, $sql)->execute($db);
	 *   // ... more code/queries
	 *   $db->commit();
	 * }
	 * catch (Exception $e)
	 * {
	 *   $db->rollback();
	 * }
	 * 
	 * // 3) Example using the Query class without the default database
	 * $db = Database::instance('alternate');
	 * Query::begin($db);
	 * Query::sql($sql)->execute($db);
	 * // ... more code/queries
	 * // If some conditions met:
	 * Query::commit($db);
	 * // Else
	 * Query::rollback($db);
	 * 
	 * @return boolean
	 */
	public static function begin($db = NULL)
	{
		$query = new Database_Query(NULL, NULL);
		return $query->begin($db);
	}

	/**
	 * Commit the current transaction
	 *
	 * @return  boolean
	 */
	public static function commit($db = NULL)
	{
		$query = new Database_Query(NULL, NULL);
		return $query->commit($db);
	}

	/**
	 * Abort the current transaction
	 *
	 * @return  boolean
	 */
	public static function rollback($db = NULL)
	{
		$query = new Database_Query(NULL, NULL);
		return $query->rollback($db);
	}
}