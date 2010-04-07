<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query object creation helper methods. Extends Kohana_DB.
 * 
 * Modifications by Rick Jolly: 
 * - Added method sql($sql) as an alias to DB:query($any_type, $sql)
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
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