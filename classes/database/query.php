<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query wrapper.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Database_Query extends Kohana_Database_Query {

	public $_cache = null;
	public $_cache_check = true;
	public $_cache_lifetime = null;
	
	/**
	 * Enables the query to be cached for a specified amount of time.
	 *
	 * @param   integer  number of seconds to cache or null for default
	 * @return  $this
	 */
	public function cached($lifetime = NULL)
	{
		return $this->cache(true, $lifetime);
	}
	
	/**
	 * More feature-rich caching using the optional cache library if available. The default
	 * Kohana cache doesn't support all the use cases below as expected. The default cache checks lifetime 
	 * when getting based on the file modified time.
	 * 
	 * Simplified usage for both Kohana cache and cache module:
	 * - cache() - Get/set.
	 * - cache(true, 60) - Get/set.
	 * - cache(true, 0) - Delete.
	 * - cache(false) - Refresh.
	 * - cache(false, 60) - Refresh.
	 * - cache(false, 0) - Delete.
	 * 
	 * Specific usage for cache module:
	 * - cache() - Get or set cache. Get cache. If empty, set cache using default lifetime.
	 * - cache(true, 60) - Get or set cache. Same as above cache() but using specified lifetime.
	 * - cache(true, 0) - Delete cache. Check and return cache if it exists, but also delete cache. 
	 * 	  	for this query. If a cached result doesn't exist, then same effect as not using cache().
	 * - cache(false) - Refresh cache. Don't check cache, but cache the new results using the default lifetime.
	 * - cache(false, 60) - Refresh cache. Same as above cache(false) but using specified lifetime.
	 * - cache(false, 0) - Delete cache. Don't check cache and delete cache for this query if it was previously cached.
	 *
	 * Specific usage if no cache module. Note that lifetime is meaningless when setting cache:
	 * - cache() - 	1) Get cache. If cache exists and is younger than default lifetime, then get cache. 
	 * 					If older than default time, delete cache.
	 * 				2) Else set cache. Lifetime not used when setting.
	 * - cache(true, 60) - 	Same as above cache(), but use specified lifetime instead of default lifetime.
	 * - cache(true, 0) - Delete cache. Doesn't get or set cache.
	 * - cache(false) - Refresh cache. Don't check cache, but cache the new results ignoring lifetime.
	 * - cache(false, 60) - Refresh cache. Same as above cache(false).
	 * - cache(false, 0) - Delete cache. Same as cache(true, 0);
	 * 
	 * @param boolean $check [optional] Check cache and return cached result if available.
	 * @param integer $specific_lifetime [optional] Set cache lifetime. If null, use default. If "0", delete.
	 * @param string $type [optional] Cache type if using cache module. If null use default.
	 * @return 
	 */
	public function cache($check = TRUE, $specific_lifetime = NULL, $type = NULL)
	{
		if (! isset($this->_cache))
		{
			// TODO: is this the best way to check for the "cache" module?
			$modules = Kohana::modules();
			if (isset($modules['cache']))
			{
				// use the "unofficial" Kohana cache module
				$this->_cache = Cache::instance($type);
			}
			else
			{
				// Default internal Kohana cache
				$this->_cache = true;
			}
		}
		
		if ($specific_lifetime === NULL)
		{
			if (is_object($this->_cache))
			{
				// Use the default internal Kohana cache lifetime which is 60 seconds.
				$this->_cache_lifetime = 60;
			}
			else
			{
				// Use the default lifetime from the Cache module.
				$this->_cache_lifetime = Kohana::config('cache.default-expire');				
			}	
		}
		else
		{
			$this->_cache_lifetime = $specific_lifetime;
		}
		
		$this->_cache_check = $check;

		return $this;
	}

	/**
	 * Execute the current query on the given database.
	 * 
	 * NOTE: $db will no longer be set to 'default' so that the default config
	 * group cannot be loaded automatically if the default database hasn't
	 * been instantiated. This is to prevent a potentially unexpected default
	 * database when using multiple environments. See Database::default_instance();
	 * 
	 * @param   mixed  Database instance or name of instance
	 * @return  object   Database_Result for SELECT queries
	 * @return  mixed    the insert id for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	public function execute($db = NULL)
	{
		if (! isset($db))
		{
			$db = 'default';
			
			// If default database isn't yet loaded, throw an exception. It shouldn't be loaded implicitly.
			if ( ! isset(Database::$instances[$db]))
			{
				throw new Kohana_Exception('Database group name is undefined. 
					This version of Database does not instantiate the default group implicitly.');
			}
		}
		
		if (! is_object($db))
		{
			// Get the database instance
			$db = Database::instance($db);
		}

		// Compile the SQL query
		$sql = $this->compile($db);

		if (isset($this->_cache))
		{
			$result = $this->_execute_cache($db, $sql);
		}
		else
		{
			// Execute the query. Type not used in this version of Database.
			$result = $db->query(NULL, $sql, $this->_as_object);
		}
		
		return $result;
	}
	
	private function _execute_cache($db, $sql)
	{
		// Set the cache key based on the database instance name and SQL
		$cache_key = 'Database::query("'.$db.'", "'.$sql.'")';
			
		if ($this->_cache_check)
		{
echo('<br/>check cache');
			// Use the cache module.
			if (is_object($this->_cache))
			{
echo('<br/>check cache module');
				if ($result = $this->_cache->get($cache_key))
				{
echo('<br/>check cache module has cached result');
					if ($this->_cache_lifetime === 0)
					{
echo('<br/>check cache module delete cached result');
						// The Cache module will allow deletion of cache items even if they
						// haven't yet expired. If lifetime is set to "0" then invalidate the cache.
						$this->_cache->delete($cache_key);
					}
					
					return new Database_Result_Cached($result, $sql, $this->_as_object);
				}
			}
			else
			{
echo('<br/>check native cache');
if ($this->_cache_lifetime === 0)
{
echo('<br/>check native cache should delete cached result if exists');
}
				// Use the internal Kohana cache. Cache will be deleted here if lifetime is less than 
				// time() + cache modified time
				if ($result = Kohana::cache($cache_key, NULL, $this->_cache_lifetime))
				{
echo('<br/>check native cache has cached result');
					// Return a cached result
					return new Database_Result_Cached($result, $sql, $this->_as_object);
				}
			}
		}

		// Execute the query. Type not used in this version of Database.
		$result = $db->query(NULL, $sql, $this->_as_object);

		if (is_object($this->_cache))
		{
echo('<br/>cache module after new result');
			// Use the cache module.
			
			if ($this->_cache_lifetime > 0)
			{
echo('<br/>cache module set cache');
				$this->_cache->set($cache_key, $result->as_array(), $this->_cache_lifetime);
			}
			else
			{
echo('<br/>cache module delete cache');
				// Invalidate the cache in case it exists
				$this->_cache->delete($cache_key);
			}
		}
		else 
		{
echo('<br/>native cache after new result');
			// Use the default Kohana internal cache.
			
			if ($this->_cache_lifetime > 0)
			{
echo('<br/>native cache set');
				// Cache the result array using the default internal Kohana cache.
				// Setting the cache lifetime here isn't supported. Lifetime is only
				// checked when getting from cache against the file modified time.
				// $this->_cache_lifetime is only included for potential forward compatibility.
				Kohana::cache($cache_key, $result->as_array(), $this->_cache_lifetime);
			}
			else
			{
echo('<br/>native cache delete');
				// Force deletion of cache.
				Kohana::cache($cache_key, NULL, 0);
			}			
		}
		
		return $result;
	}

} // End Database_Query
