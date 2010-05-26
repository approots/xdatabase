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