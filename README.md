Extended Kohana 3 Database Library (0.1)
=======================================

This is a small library that extends and modifies just a few of the Database module's classes/methods. Since all features (but one) are optional, the module can be dropped into existing projects that use Kohana's database module.

Dependencies
------------

1. Kohana's database module. This extended database module must be defined BEFORE the native database module in the bootstrap modules array.
2. Cache module (optional)

Highlights
----------

1. Doesn't use the query type (Database::SELECT, Database::UPDATE, etc.) because it is unnecessary and potentially conflicting. Query results are returned just as expected based on the result type, not the Kohana Database query type. 
2. Uses the cache module to replace Kohana's internal cache if available and offers more flexible resultset caching. 
3. Plug and play into existing projects.

Optional Features
-----------------

1. A new class class named Query extends DB and can be used as a functionally identical replacement. The new name better represents the purpose of the class. Also, DB is semantically identical to Database.
2. Query "type" is not necessary, and even if used it is ignored. Both driver classes (mysql and pdo) detect which of the 3 types of Kohana database results to return automatically (a resultset, an array containing insert id and rows affected, or an integer rows affected). So Database::SELECT, Database::INSERT, etc. are no longer used.
3. The Query class has one new method called sql() which accepts one sql string parameter. Query::sql($sql) can replace DB::query(NULL, $sql), or DB::query(Database::SELECT, $sql), etc. They are functionally equivalent. 
4. Database_Query has a new method called cache(). It is more flexible than the existing cached() method for two reasons. If available, it uses the cache module as opposed to Kohana's default internal cache. Also, in addition to the integer "lifetime" parameter, it accepts an additional boolean parameter "check". So cache can now be retrieved, set, deleted, and refreshed using different combinations of the two parameters. The cached() method is still available and, even though it now calls cache(), the results are unchanged and as expected.  
5. The Database class has an additional helper method for setting the default database called "default_instance". When using different database environments, like development and production, it is necessary to set the default database automatically. There are several methods, but Database::default_instance($config_group_name_or_config_array) is straightforward.

Mandatory Feature
-----------------

Only one feature may affect existing projects. The default database must be loaded and instantiated in the Database class before calling the Database_Query execute() method. See Database::default_instance(). This prevents Kohana from instantiating the default database using the default config group implicitly. This is to avoid potentially using an unexpected default database when multiple environments are used.

Usage
=====

IMPORTANT: This module must be defined BEFORE the native database module in the bootstrap modules array.

Query class
-----------

Since the Query class extends DB, its usage is the same:
- Query::select()->from('my_table')->execute();

Query has one new method called sql(). It is functionally identical to DB::query($type, $sql):
- Query::sql($sql)->execute();
- Query::sql($sql)->param(':my_param',$my_param)->execute();

Database_Query cache(boolean $check, integer $lifetime string $type = NULL) Method
----------------------------------------------------------------------------------

The $check boolean param is used to check for a cache value.
The integer $lifetime param is to set the $lifetime, or use the default if null, or delete cache if 0.
The string $type method is to set the cache driver to something other than the default when using the cache module.

Example:
- Query::sql($sql)->cache()->execute();
- Query::select()->from('my_table')->cache(true,3600)->execute();
- DB::select()->from('my_table')->cache()->execute();

Simplified usage for both Kohana cache and cache module:
- cache(); - Get/set.
- cache(true, 60); - Get/set.
- cache(true, 0); - Delete.
- cache(false); - Refresh.
- cache(false, 60); - Refresh.
- cache(false, 0); - Delete.

Specific usage for cache module:
- cache(); - Get or set cache. Get cache. If empty, set cache using default lifetime.
- cache(true, 60); - Get or set cache. Same as above cache() but using specified lifetime.
- cache(true, 0); - Delete cache. Check and return cache if it exists, but also delete cache for this query. If cache doesn't exist, then same effect as not using cache().
- cache(false); - Refresh cache. Don't check cache, but cache the new results using the default lifetime.
- cache(false, 60); - Refresh cache. Same as above cache(false) but using specified lifetime.
- cache(false, 0); - Delete cache. Don't check cache and delete cache for this query if it was previously cached.
- cache(true, 60, 'memcache'); - Use a specific cache driver.

Specific usage if no cache module. Note that lifetime is meaningless when setting cache:
- cache(); -  Get or set cache. 
			1) If cache exists and is younger than default lifetime, then get cache. If older than default time, delete cache.
 		    2) Else set cache. Lifetime not used when setting.
- cache(true, 60); - Same as above cache(), but use specified lifetime instead of default lifetime.
- cache(true, 0); - Delete cache. Doesn't get or set cache.
- cache(false); - Refresh cache. Don't check cache, but cache the new results ignoring lifetime.
- cache(false, 60); - Refresh cache. Same as above cache(false) since lifetime ignored when setting.
- cache(false, 0); - Delete cache. Same as cache(true, 0);

Database::default_instance()
----------------------------

Use this to set the default database in the bootstrap:
- Database::default_instance($config_group_name_or_config_array);

For example if you have Kohana::environment set to "development" and your database config group has the same name:
- Database::default_instance(Kohana::$environment);

Some alternatives to this method:
1. Put logic in config files to dynamically set the "default" group to some other group.
2. In bootstrap: Kohana::config('database')->default = Kohana::config('database')->$group_name;. Note that all config parameters must be defined since Kohana will not be able to merge config files.
3. In bootstrap: Database::instance('default', Kohana::config('database')->$group_name);. This works but isn't very clear.
