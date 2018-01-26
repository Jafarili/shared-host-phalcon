<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\Exception;
use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend\Redis
 *
 * Allows to cache output fragments, PHP data or raw data to a redis backend
 *
 * This adapter uses the special redis key "_PHCR" to store all the keys internally used by the adapter
 *
 *<code>
 * use Phalcon\Cache\Backend\Redis;
 * use Phalcon\Cache\Frontend\Data as FrontData;
 *
 * // Cache data for 2 days
 * $frontCache = new FrontData(
 *     [
 *         "lifetime" => 172800,
 *     ]
 * );
 *
 * // Create the Cache setting redis connection options
 * $cache = new Redis(
 *     $frontCache,
 *     [
 *         "host"       => "localhost",
 *         "port"       => 6379,
 *         "auth"       => "foobared",
 *         "persistent" => false,
 *         "index"      => 0,
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

class Redis extends Backend {

    protected $_redis;

    /***
	 * Phalcon\Cache\Backend\Redis constructor
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
			$options["port"] = 6379;
		}

		if ( !isset options["index"] ) {
			$options["index"] = 0;
		}

		if ( !isset options["persistent"] ) {
			$options["persistent"] = false;
		}

		if ( !isset options["statsKey"] ) {
			// Disable tracking of cached keys per default
			$options["statsKey"] = "";
		}

		if ( !isset options["auth"] ) {
			$options["auth"] = "";
		}

		parent::__construct(frontend, options);
    }

    /***
	 * Create internal connection to redis
	 **/
    public function _connect() {

		$options = $this->_options;
		$redis = new \Redis();

		if ( !fetch host, options["host"] || !fetch port, options["port"] || !fetch persistent, options["persistent"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( persistent ) {
			$success = redis->pconnect(host, port);
		} else {
			$success = redis->connect(host, port);
		}

		if ( !success ) {
			throw new Exception("Could not connect to the Redisd server ".host.":".port);
		}

		if ( fetch auth, options["auth"] && !empty options["auth"] ) {
			$success = redis->auth(auth);

			if ( !success ) {
				throw new Exception("Failed to authenticate with the Redisd server");
			}
		}

		if ( fetch index, options["index"] && index > 0 ) {
			$success = redis->select(index);

			if ( !success ) {
				throw new Exception("Redis server selected database failed");
			}
		}

		$this->_redis = redis;
    }

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$redis = $this->_redis;
		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		$frontend = $this->_frontend;
		$prefix = $this->_prefix;
		$lastKey = "_PHCR" . prefix . keyName;
		$this->_lastKey = lastKey;
		$cachedContent = redis->get(lastKey);

		if ( cachedContent === false ) {
			return null;
		}

		if ( is_numeric(cachedContent) ) {
			return cachedContent;
		}

		return frontend->afterRetrieve(cachedContent);
    }

    /***
	 * Stores cached content into the file backend and stops the frontend
	 *
	 * <code>
	 * $cache->save("my-key", $data);
	 *
	 * // Save data termlessly
	 * $cache->save("my-key", $data, -1);
	 * </code>
	 *
	 * @param int|string keyName
	 * @param string content
	 * @param int lifetime
	 * @param boolean stopBuffer
	 **/
    public function save($keyName  = null , $content  = null , $lifetime  = null , $stopBuffer  = true ) {
			tmp, tt1, success, options, specialKey, isBuffering;

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
			$prefixedKey = substr(lastKey, 5);
		} else {
			$prefixedKey = $this->_prefix . keyName,
				lastKey = "_PHCR" . prefixedKey,
				this->_lastKey = lastKey;
		}

		if ( !lastKey ) {
			throw new Exception("The cache must be started first");
		}

		$frontend = $this->_frontend;

		/**
		 * Check if ( a connection is created or make a new one
		 */
		$redis = $this->_redis;
		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
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

		$success = redis->set(lastKey, preparedContent);

		if ( !success ) {
			throw new Exception("Failed storing the data in redis");
		}

		// Don't set expiration for ( negative ttl or zero
		if ( tt1 >= 1 ) {
			redis->settimeout(lastKey, tt1);
		}

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey != "" ) {
			redis->sAdd(specialKey, prefixedKey);
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
	 **/
    public function delete($keyName ) {

		$redis = $this->_redis;
		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		$prefix = $this->_prefix;
		$prefixedKey = prefix . keyName;
		$lastKey = "_PHCR" . prefixedKey;
		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey != "" ) {
			redis->sRem(specialKey, prefixedKey);
		}

		/**
		* Delete the key from redis
		*/
		return (bool) redis->delete(lastKey);
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

		$redis = $this->_redis;

		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCR')!");
		}

		/**
		* Get the key from redis
		*/
		$keys = redis->sMembers(specialKey);
		if ( gettype($keys) != "array" ) {
			return [];
		}

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
			$lastKey = "_PHCR" . prefix . keyName;
		}

		if ( lastKey ) {
			$redis = $this->_redis;
			if ( gettype($redis) != "object" ) {
				this->_connect();
				$redis = $this->_redis;
			}

			return (bool) redis->exists(lastKey);
		}

		return false;
    }

    /***
	 * Increment of given $keyName by $value
	 *
	 * @param string keyName
	 **/
    public function increment($keyName  = null , $value  = 1 ) {

		$redis = $this->_redis;

		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = "_PHCR" . prefix . keyName;
			$this->_lastKey = lastKey;
		}

		return redis->incrBy(lastKey, value);
    }

    /***
	 * Decrement of $keyName by given $value
	 *
	 * @param string keyName
	 **/
    public function decrement($keyName  = null , $value  = 1 ) {

		$redis = $this->_redis;

		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = "_PHCR" . prefix . keyName;
			$this->_lastKey = lastKey;
		}

		return redis->decrBy(lastKey, value);
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {

		$options = $this->_options;

		if ( !fetch specialKey, options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		$redis = $this->_redis;

		if ( gettype($redis) != "object" ) {
			this->_connect();
			$redis = $this->_redis;
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCR')!");
		}

		$keys = redis->sMembers(specialKey);
		if ( gettype($keys) == "array" ) {
			foreach ( $keys as $key ) {
				$lastKey = "_PHCR" . key;
				redis->sRem(specialKey, key);
				redis->delete(lastKey);
			}
		}

		return true;
    }

}