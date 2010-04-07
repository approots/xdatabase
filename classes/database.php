<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection wrapper. Extends Kohana_Database.
 * 
 * Modifications by Rick Jolly: 
 * - Added method default_instance($default)
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Database extends Kohana_Database {
	
	/**
	 * Get/set the "default" database instance. The same thing could be done
	 * using Database::instance(). This method is just for convenience and clarity.
	 * Normally, this method should be called to set the default database immediately 
	 * (in the bootstrap).
	 * 
	 * @param mixed $default config group string, config array, or null if not setting
	 * @return 
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