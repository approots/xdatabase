<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection wrapper. Extends Kohana_Database.
 * 
 * - Added method default_instance($default)
 *
 * @package Database
 * @author Rick Jolly <rickjolly@hotmail.com>
 * @copyright (c) 2010 Rick Jolly
 * @license ISC http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Database extends Kohana_Database {
	
	/**
	 * Get/set the "default" database instance. The same thing could be done
	 * using Database::instance(). This method is just for convenience and clarity.
	 * Normally, this method should be called to set the default database immediately 
	 * (in the bootstrap).
	 * 
	 * @param mixed $default config group string, config array, or null if not setting
	 * @return object default database instance
	 */
	public static function default_instance($default)
	{
		if (( ! isset(Database::$instances['default'])) AND (isset($default)))
		{
			if ( ! is_array($default))
			{
				// Load the configuration for this database.
				// $default could be 'production' or 'development' for example.
				$default = Kohana::config('database')->$default;
			}
			// Instantiate, add, and return the default database.
			return Database::instance('default', $default);
		}
		
		return Database::$instances['default'];
	}
}