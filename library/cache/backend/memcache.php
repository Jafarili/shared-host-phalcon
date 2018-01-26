<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\Exception;
use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend\Memcache
 *
 * Allows to cache output fragments, PHP data or raw data to a memcache backend
 *
 * This adapter uses the special memcached key "_PHCM" to store all the keys internally used by the adapter
 *
 *<code>
 * use Phalcon\Cache\Backend\Memcache;
 * use Phalcon\Cache\Frontend\Data as FrontData;
 *
 * // Cache data for 2 days
 * $frontCache = new FrontData(
 *     [
 *         "lifetime" => 172800,
 *     ]
 * );
 *
 * // Create the Cache setting memcached connection options
 * $cache = new Memcache(
 *     $frontCache,
 *     [
 *         "host"       => "localhost",
 *         "port"       => 11211,
 *         "persistent" => false,
 *     ]
 * );
 *
 * // Cache arbitrary data
 * $cache->save("my-data", [1, 2, 3, 4, 5]);
 *
 * // Get data
 * $data = $cache->get("my-data");
 *</code>
 **/

class Memcache extends Backend {

    protected $_memcache;

    /***
	 * Phalcon\Cache\Backend\Memcache constructor
	 *
	 * @param	Phalcon\Cache\FrontendInterface frontend
	 * @param	array options
	 **/
    public function __construct($frontend , $options  = null ) {
		if ( gettype($options) != "array" ) {
			$options = [];
		}

		if ( !isset options["host"] ) {
			$options["host"] = "127.0.0.1";
		}

		if ( !isset options["port"] ) {
			$options["port"] = 11211;
		}

		if ( !isset options["persistent"] ) {
			$options["persistent"] = false;
		}

		if ( !isset options["statsKey"] ) {
			// Disable tracking of cached keys per default
			$options["statsKey"] = "";
		}

		parent::__construct(frontend, options);
    }

    /***
	 * Create internal connection to memcached
	 **/
    public function _connect() {

		$options = $this->_options;
		$memcache = new \Memcache();

		if ( !fetch host, options["host"] || !fetch port, options["port"] || !fetch persistent, options["persistent"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( persistent ) {
			$success = memcache->pconnect(host, port);
		} else {
			$success = memcache->connect(host, port);
		}

		if ( !success ) {
			throw new Exception("Cannot connect to Memcached server");
		}

		$this->_memcache = memcache;
    }

    /***
	 * Add servers to memcache pool
	 **/
    public function addServers($host , $port , $persistent  = false ) {
		/**
		 * Check if ( a connection is created or make a new one
		 */
		$memcache = $this->_memcache;
		if ( gettype($memcache) != "object" ) {
		    $this->_connect();
		    $memcache = $this->_memcache;
		}
		$success = memcache->addServer(host, port, persistent);
		$this->_memcache = memcache;
		return success;
    }

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$memcache = $this->_memcache;
		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		$prefixedKey = $this->_prefix . keyName;
		$this->_lastKey = prefixedKey;
		$cachedContent = memcache->get(prefixedKey);

		if ( cachedContent === false ) {
			return null;
		}

		if ( is_numeric(cachedContent) ) {
			return cachedContent;
		}

		$retrieve = $this->_frontend->afterRetrieve(cachedContent);
		return retrieve;
    }

    /***
	 * Stores cached content into the file backend and stops the frontend
	 *
	 * @param int|string keyName
	 * @param string content
	 * @param int lifetime
	 * @param boolean stopBuffer
	 **/
    public function save($keyName  = null , $content  = null , $lifetime  = null , $stopBuffer  = true ) {
			specialKey, keys, isBuffering;

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . keyName,
				this->_lastKey = lastKey;
		}

		if ( !lastKey ) {
			throw new Exception("Cache must be started first");
		}

		$frontend = $this->_frontend;

		/**
		 * Check if ( a connection is created or make a new one
		 */
		$memcache = $this->_memcache;
		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		if ( content === null ) {
			$cachedContent = frontend->getContent();
		} else {
			$cachedContent = content;
		}

		/**
		 * Prepare the content in the frontend
		 */
		if ( !is_numeric(cachedContent) ) {
			$preparedContent = frontend->befor (eStore(cachedContent);
		} else {
			$preparedContent = cachedContent;
		}

		if ( lif (etime === null ) {
			$tmp = $this->_lastLif (etime;

			if ( !tmp ) {
				$ttl = frontend->getLif (etime();
			} else {
				$ttl = tmp;
			}
		} else {
			$ttl = lif (etime;
		}

		/**
		* We store without flags
		*/
		$success = memcache->set(lastKey, preparedContent, 0, ttl);

		if ( !success ) {
			throw new Exception("Failed storing data in memcached");
		}

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey != "" ) {
			/**
			 * Update the stats key
			 */
			$keys = memcache->get(specialKey);
			if ( gettype($keys) != "array" ) {
				$keys = [];
			}

			if ( !isset($keys[lastKey]) ) {
				$keys[lastKey] = ttl;
				memcache->set(specialKey, keys);
			}
		}

		$isBuffering = frontend->isBuffering();

		if ( stopBuffer === true ) {
			frontend->stop();
		}

		if ( isBuffering === true ) {
			echo cachedContent;
		}

		$this->_started = false;

		return success;
    }

    /***
	 * Deletes a value from the cache by its key
	 *
	 * @param int|string keyName
	 * @return boolean
	 **/
    public function delete($keyName ) {

		$memcache = $this->_memcache;
		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		$prefixedKey = $this->_prefix . keyName;
		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey != "" ) {
			$keys = memcache->get(specialKey);

			if ( gettype($keys) == "array" ) {
				unset keys[prefixedKey];
				memcache->set(specialKey, keys);
			}
		}

		/**
		 * Delete the key from memcached
		 */
		$ret = memcache->delete(prefixedKey);
		return ret;
    }

    /***
	 * Query the existing cached keys.
	 *
	 * <code>
	 * $cache->save("users-ids", [1, 2, 3]);
	 * $cache->save("projects-ids", [4, 5, 6]);
	 *
	 * var_dump($cache->queryKeys("users")); // ["users-ids"]
	 * </code>
	 **/
    public function queryKeys($prefix  = null ) {

		$memcache = $this->_memcache;

		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCM')!");
		}

		/**
		 * Get the key from memcached
		 */
		$keys = memcache->get(specialKey);
		if ( unlikely gettype($keys) != "array" ) {
			return [];
		}

		$keys = array_keys(keys);
		foreach ( idx, $keys as $key ) {
			if ( !empty prefix && !starts_with(key, prefix) ) {
				unset keys[idx];
			}
		}

		return keys;
    }

    /***
	 * Checks if cache exists and it isn't expired
	 *
	 * @param string keyName
	 * @param int lifetime
	 **/
    public function exists($keyName  = null , $lifetime  = null ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . keyName;
		}

		if ( lastKey ) {

			$memcache = $this->_memcache;
			if ( gettype($memcache) != "object" ) {
				this->_connect();
				$memcache = $this->_memcache;
			}

			if ( !memcache->get(lastKey) ) {
				return false;
			}
			return true;
		}

		return false;
    }

    /***
	 * Increment of given $keyName by $value
	 *
	 * @param string keyName
	 **/
    public function increment($keyName  = null , $value  = 1 ) {

		$memcache = $this->_memcache;

		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . keyName;
			$this->_lastKey = lastKey;
		}

		return memcache->increment(lastKey, value);
    }

    /***
	 * Decrement of $keyName by given $value
	 *
	 * @param string keyName
	 **/
    public function decrement($keyName  = null , $value  = 1 ) {

		$memcache = $this->_memcache;

		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . keyName;
			$this->_lastKey = lastKey;
		}

		return memcache->decrement(lastKey, value);
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {

		$memcache = $this->_memcache;

		if ( gettype($memcache) != "object" ) {
			this->_connect();
			$memcache = $this->_memcache;
		}

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCM')!");
		}

		/**
		 * Get the key from memcached
		 */
		$keys = memcache->get(specialKey);
		if ( unlikely gettype($keys) != "array" ) {
			return true;
		}

		foreach ( key, $keys as $_ ) {
			memcache->delete(key);
		}

		memcache->delete(specialKey);

		return true;
    }

}