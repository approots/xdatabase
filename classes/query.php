<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query object creation helper methods. Extends Kohana DB.
 * 
 * Modifications by Rick Jolly: 
 * - Added method sql($sql) as an alias to DB:query(NULL, $sql)
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Query extends DB {
	
	public static function sql($sql)
	{	
		return new Database_Query(null, $sql);
	}

}