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
}