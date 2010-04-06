<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection wrapper.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Database extends Kohana_Database {
	
	/**
	 * @var  array  Database instances
	 */
	//public static $instances = array();
	
	/**
	 * Get/set the "default" database instance. This is a convenience method for, as
	 * an example, this:
	 * 
	 * Database::instance('default', Kohana::config('database')->development);
	 * 
	 * Normally, this method should be called to set the default database immediately 
	 * (in the bootstrap).
	 * 
	 * @param mixed $default: config group string, config array, or null if not setting
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
	
	/*
	public static function instance($name = 'default', array $config = NULL)
	{
		
		// Modification begins
		if ( ! isset($name))
		{
			$name = 'default';
			
			// This version of Database doesn't load the "default" configuration 
			// and instantiate the default database unless $name is explicitly set 
			// to "default" in the modified Database_Query execute() method. In the 
			// modified Database_Query execute() method, when no database parameter is 
			// passed, it will pass NULL into this Database::instance() method. These changes 
			// prevent the default group from being loaded and instantiated from 
			// the "default" config group implicitly. Configs should only be loaded
			// by defining the config group explicitly.
			if ( ! isset(Database::$instances[$name]))
			{
				throw new Kohana_Exception('Database group name is undefined. 
					This version of Database does not instantiate the default group implicitly.');
			}
		} 
		// Modification ends
		else if ( ! isset(Database::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration for this database
				$config = Kohana::config('database')->$name;			
			}

			if ( ! isset($config['type']))
			{
				throw new Kohana_Exception('Database type not defined in :name configuration',
					array(':name' => $name));
			}			
			
			// Set the driver class name
			$driver = 'Database_'.ucfirst($config['type']);

			// Create the database connection instance
			new $driver($name, $config);
		}

		return Database::$instances[$name];
	}
	*/
}