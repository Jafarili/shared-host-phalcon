<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\Exception;
use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend\Xcache
 *
 * Allows to cache output fragments, PHP data and raw data using an XCache backend
 *
 *<code>
 * use Phalcon\Cache\Backend\Xcache;
 * use Phalcon\Cache\Frontend\Data as FrontData;
 *
 * // Cache data for 2 days
 * $frontCache = new FrontData(
 *     [
 *        "lifetime" => 172800,
 *     ]
 * );
 *
 * $cache = new Xcache(
 *     $frontCache,
 *     [
 *         "prefix" => "app-data",
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

class Xcache extends Backend {

    /***
	 * Phalcon\Cache\Backend\Xcache constructor
	 *
	 * @param \Phalcon\Cache\FrontendInterface frontend
	 * @param array options
	 **/
    public function __construct($frontend , $options  = null ) {
		if ( gettype($options) != "array" ) {
			$options = [];
		}

		if ( !isset options["statsKey"] ) {
			// Disable tracking of cached keys per default
			$options["statsKey"] = "";
		}

		parent::__construct(frontend, options);
    }

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$frontend = $this->_frontend;
		$prefixedKey = "_PHCX" . $this->_prefix . keyName;
		$this->_lastKey = prefixedKey;
		$cachedContent = xcache_get(prefixedKey);

		if ( !cachedContent ) {
			return null;
		}

		if ( is_numeric(cachedContent) ) {
			return cachedContent;
		} else {
			return frontend->afterRetrieve(cachedContent);
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
			options, keys, specialKey;

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = "_PHCX" . $this->_prefix . keyName,
				this->_lastKey = lastKey;
		}

		if ( !lastKey ) {
			throw new Exception("Cache must be started first");
		}

		$frontend = $this->_frontend;
		if ( content === null ) {
			$cachedContent = frontend->getContent();
		} else {
			$cachedContent = content;
		}

		if ( !is_numeric(cachedContent) ) {
			$preparedContent = frontend->befor (eStore(cachedContent);
		} else {
			$preparedContent = cachedContent;
		}

		/**
		 * Take the lif (etime from the frontend or read it from the set in start()
		 */
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

		$success = xcache_set(lastKey, preparedContent, tt1);

		if ( !success ) {
			throw new Exception("Failed storing the data in xcache");
		}

		$isBuffering = frontend->isBuffering();

		if ( stopBuffer === true ) {
			frontend->stop();
		}

		if ( isBuffering === true ) {
			echo cachedContent;
		}

		$this->_started = false;

		if ( success ) {
			$options = $this->_options;

			if ( !fetch specialKey, $this->_options["statsKey"] ) {
				throw new Exception("Unexpected inconsistency in options");
			}

			if ( specialKey != "" ) {
				/**
				 * xcache_list() is available only to the administrator (unless XCache was
				 * patched). We have to update the list of the stored keys.
				 */
				$keys = xcache_get(specialKey);
				if ( gettype($keys) != "array" ) {
					$keys = [];
				}

				$keys[lastKey] = tt1;
				xcache_set(specialKey, keys);
			}
		}

		return success;
    }

    /***
	 * Deletes a value from the cache by its key
	 *
	 * @param int|string keyName
	 * @return boolean
	 **/
    public function delete($keyName ) {

		$prefixedKey = "_PHCX" . $this->_prefix . keyName;

		if ( !fetch specialKey, $this->_options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey != "" ) {
			$keys = xcache_get(specialKey);
			if ( gettype($keys) != "array" ) {
				$keys = [];
			}

			unset keys[prefixedKey];

			xcache_set(specialKey, keys);
		}
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

		if ( !prefix ) {
			$prefixed = "_PHCX";
		} else {
			$prefixed = "_PHCX" . prefix;
		}

		$options = $this->_options;

		if ( !fetch specialKey, $this->_options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCX')!");
		}

		/**
		 * Get the key from XCache (we cannot use xcache_list() as it is available only to
		 * the administrator)
		 */
		$keys = xcache_get(specialKey);
		if ( gettype($keys) != "array" ) {
			return [];
		}

		$retval = [];

		foreach ( key, $keys as $_ ) {
			if ( starts_with(key, prefixed) ) {
				$realKey = substr(key, 5);
				$retval[] = realKey;
			}
		}

		return retval;
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
			$lastKey = "_PHCX" . $this->_prefix . keyName;
		}

		if ( lastKey ) {
			return xcache_isset(lastKey);
		}
		return false;
    }

    /***
	* Atomic increment of a given key, by number $value
	*
	* @param string keyName
	**/
    public function increment($keyName , $value  = 1 ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = "_PHCX" . $this->_prefix . keyName;
		}

		if ( !lastKey ) {
			throw new Exception("Cache must be started first");
		}

		if ( function_exists("xcache_inc") ) {
			$newVal = xcache_inc(lastKey, value);
		} else {
			$origVal = xcache_get(lastKey);
			$newVal = origVal - value;
			xcache_set(lastKey, newVal);
		}

		return newVal;
    }

    /***
	 * Atomic decrement of a given key, by number $value
	 *
	 * @param string keyName
	 **/
    public function decrement($keyName , $value  = 1 ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = "_PHCX" . $this->_prefix . keyName;
		}

		if ( !lastKey ) {
			throw new Exception("Cache must be started first");
		}

		if ( function_exists("xcache_dec") ) {
			$newVal = xcache_dec(lastKey, value);
		} else {
			$origVal = xcache_get(lastKey);
			$newVal = origVal - value;
			$success = xcache_set(lastKey, newVal);
		}

		return newVal;
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {

		$options = $this->_options;

		if ( !fetch specialKey, $this->_options["statsKey"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( specialKey == "" ) {
			throw new Exception("Cached keys need to be enabled to use this function (options['statsKey'] == '_PHCM')!");
		}

		$keys = xcache_get(specialKey);

		if ( gettype($keys) == "array" ) {
			foreach ( key, $keys as $_ ) {
				unset keys[key];
				xcache_unset(key);
			}
			xcache_set(specialKey, keys);
		}

		return true;
    }

}