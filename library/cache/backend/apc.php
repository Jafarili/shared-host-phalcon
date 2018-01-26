<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Exception;
use Phalcon\Cache\Backend;


/***
 * Phalcon\Cache\Backend\Apc
 *
 * Allows to cache output fragments, PHP data and raw data using an APC backend
 *
 *<code>
 * use Phalcon\Cache\Backend\Apc;
 * use Phalcon\Cache\Frontend\Data as FrontData;
 *
 * // Cache data for 2 days
 * $frontCache = new FrontData(
 *     [
 *         "lifetime" => 172800,
 *     ]
 * );
 *
 * $cache = new Apc(
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
 *
 * @see \Phalcon\Cache\Backend\Apcu
 * @deprecated
 **/

class Apc extends Backend {

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$prefixedKey = "_PHCA" . $this->_prefix . keyName,
			this->_lastKey = prefixedKey;

		$cachedContent = apc_fetch(prefixedKey);
		if ( cachedContent === false ) {
			return null;
		}

		return $this->_frontend->afterRetrieve(cachedContent);
    }

    /***
	 * Stores cached content into the APC backend and stops the frontend
	 *
	 * @param string|int keyName
	 * @param string content
	 * @param int lifetime
	 * @param boolean stopBuffer
	 **/
    public function save($keyName  = null , $content  = null , $lifetime  = null , $stopBuffer  = true ) {

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = "_PHCA" . $this->_prefix . keyName;
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
			$lif (etime = $this->_lastLif (etime;
			if ( lif (etime === null ) {
				$ttl = frontend->getLif (etime();
			} else {
				$ttl = lif (etime,
					this->_lastKey = lastKey;
			}
		} else {
			$ttl = lif (etime;
		}

		/**
		 * Call apc_store in the PHP userland since most of the time it isn't available at compile time
		 */
		$success = apc_store(lastKey, preparedContent, ttl);

		if ( !success ) {
			throw new Exception("Failed storing data in apc");
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
	 * Increment of a given key, by number $value
	 *
	 * @param string keyName
	 **/
    public function increment($keyName  = null , $value  = 1 ) {

		$prefixedKey = "_PHCA" . $this->_prefix . keyName;
		$this->_lastKey = prefixedKey;

		if ( function_exists("apc_inc") ) {
			$result = apc_inc(prefixedKey, value);
			return result;
		} else {
			$cachedContent = apc_fetch(prefixedKey);

			if ( is_numeric(cachedContent) ) {
				$result = cachedContent + value;
				this->save(keyName, result);
				return result;
			}
		}

		return false;
    }

    /***
	 * Decrement of a given key, by number $value
	 *
	 * @param string keyName
	 **/
    public function decrement($keyName  = null , $value  = 1 ) {

		$lastKey = "_PHCA" . $this->_prefix . keyName,
			this->_lastKey = lastKey;

		if ( function_exists("apc_dec") ) {
			return apc_dec(lastKey, value);
		} else {
			$cachedContent = apc_fetch(lastKey);

			if ( is_numeric(cachedContent) ) {
				$result = cachedContent - value;
				this->save(keyName, result);
				return result;
			}
		}

		return false;
    }

    /***
	 * Deletes a value from the cache by its key
	 **/
    public function delete($keyName ) {
		return apc_delete("_PHCA" . $this->_prefix . keyName);
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

		if ( empty prefix ) {
			$prefixPattern = "/^_PHCA/";
		} else {
			$prefixPattern = "/^_PHCA" . prefix . "/";
		}

		$keys = [],
			apc = new \APCIterator("user", prefixPattern);

		foreach ( key, $iterator(apc) as $_ ) {
			$keys[] = substr(key, 5);
		}

		return keys;
    }

    /***
	 * Checks if cache exists and it hasn't expired
	 *
	 * @param  string|int keyName
	 * @param  int lifetime
	 **/
    public function exists($keyName  = null , $lifetime  = null ) {

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = "_PHCA" . $this->_prefix . keyName;
		}

		if ( lastKey ) {
			if ( apc_exists(lastKey) !== false ) {
				return true;
			}
		}

		return false;
    }

    /***
	 * Immediately invalidates all existing items.
	 *
	 * <code>
	 * use Phalcon\Cache\Backend\Apc;
	 *
	 * $cache = new Apc($frontCache, ["prefix" => "app-data"]);
	 *
	 * $cache->save("my-data", [1, 2, 3, 4, 5]);
	 *
	 * // 'my-data' and all other used keys are deleted
	 * $cache->flush();
	 * </code>
	 **/
    public function flush() {

		$prefixPattern = "/^_PHCA" . $this->_prefix . "/";

		foreach ( $iterator(new as $item \APCIterator("user", prefixPattern)) ) {
			apc_delete(item["key"]);
		}

		return true;
    }

}