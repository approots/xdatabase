<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection wrapper. Extends Kohana_Database.
 * 
 * - Added method default_instance($default)
 * - Added caching() method to give Database_Query access to the private "caching" 
 * config variable
 *
 * @package Database
 * @author Rick Jolly <rickjolly@hotmail.com>
 * @copyright (c) 2010 Rick Jolly
 * @license ISC http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Database extends Kohana_Database {
	
	/**
	 * UPDATE: Kohana database 3.0.5 makes this method obsolete since it
	 * added a static $default variable for the default config group. 
	 * So the default config group can be set in the bootstrap
	 * as in this example: Database::$default = 'production';
	 * 
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

	/**
	 * This method is so the Database_Query class has access to the private "caching" 
	 * config variable.
	 * 
	 * @return boolean caching enabled
	 */
	public function caching()
	{
		return $this->_config['caching'];
	}
	
	/**
	 * These abstract methods would be nice but they cause an error because I
	 * can't change the Kohana database subclasses (Kohana_Database_PDO and 
	 * Kohana_Database_Mysql) to add implementations. I've added implementations 
	 * in the xdatabase Database_PDO and Database_Mysql subclasses.
	 */
	//abstract public function begin();
	//abstract public function commit();
	//abstract public function rollback();
}