<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\FrontendInterface;
use Phalcon\Cache\Exception;


/***
 * Phalcon\Cache\Backend\Libmemcached
 *
 * Allows to cache output fragments, PHP data or raw data to a libmemcached backend.
 * Per default persistent memcached connection pools are used.
 *
 *<code>
 * use Phalcon\Cache\Backend\Libmemcached;
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
 * $cache = new Libmemcached(
 *     $frontCache,
 *     [
 *         "servers" => [
 *             [
 *                 "host"   => "127.0.0.1",
 *                 "port"   => 11211,
 *                 "weight" => 1,
 *             ],
 *         ],
 *         "client" => [
 *             \Memcached::OPT_HASH       => \Memcached::HASH_MD5,
 *             \Memcached::OPT_PREFIX_KEY => "prefix.",
 *         ],
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

class Libmemcached extends Backend {

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

		if ( !isset options["servers"] ) {
			$servers = [0: ["host": "127.0.0.1", "port": 11211, "weight": 1]];
			$options["servers"] = servers;
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

		/* Enable persistent memcache connection per default */
		if ( !fetch persistentId, options["persistent_id"] ) {
			$persistentId = "phalcon_cache";
		}

		/* Get memcached pool connection */
		$memcache = new \Memcached(persistentId);

		/* Persistent memcached pools need to be reconnected if ( getServerList() is empty */
		if ( empty memcache->getServerList() ) {
			if ( !fetch servers, options["servers"] ) {
				throw new Exception("Servers must be an array");
			}

			if ( gettype($servers) != "array" ) {
				throw new Exception("Servers must be an array");
			}

			if ( !fetch client, options["client"] ) {
				$client = [];
			}

			if ( gettype($client) !== "array" ) {
				throw new Exception("Client options must be instance of array");
			}

			if ( !memcache->setOptions(client) ) {
				throw new Exception("Cannot set to Memcached options");
			}

			if ( !memcache->addServers(servers) ) {
				throw new Exception("Cannot connect to Memcached server");
			}
		}

		$this->_memcache = memcache;
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
		if ( !cachedContent ) {
			return null;
		}

		if ( is_numeric(cachedContent) ) {
			return cachedContent;
		} else {
			return $this->_frontend->afterRetrieve(cachedContent);
		}
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
				$tt1 = frontend->getLif (etime();
			} else {
				$tt1 = tmp;
			}
		} else {
			$tt1 = lif (etime;
		}

		$success = memcache->set(lastKey, preparedContent, tt1);

		if ( !success ) {
			throw new Exception("Failed storing data in memcached, error code: " . memcache->getResultCode());
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
				$keys[lastKey] = tt1;
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
			$lastKey = $this->_prefix . keyName;
		}

		if ( lastKey ) {
			$memcache = $this->_memcache;
			if ( gettype($memcache) != "object" ) {
				this->_connect();
				$memcache = $this->_memcache;
			}
			$value = memcache->get(lastKey);
			if ( !value ) {
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

		if ( !value ) {
			$value = 1;
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
	 *
	 * Memcached does not support flush() per default. If you require flush() support, set $config["statsKey"].
     * All modified keys are stored in "statsKey". Note: statsKey has a negative performance impact.
     *
     *<code>
     * $cache = new \Phalcon\Cache\Backend\Libmemcached(
     *     $frontCache,
     *     [
     *         "statsKey" => "_PHCM",
     *     ]
     * );
     *
     * $cache->save("my-data", [1, 2, 3, 4, 5]);
     *
     * // 'my-data' and all other used keys are deleted
     * $cache->flush();
     *</code>
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