<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\Exception;


/***
 * Phalcon\Cache\Backend\Memory
 *
 * Stores content in memory. Data is lost when the request is finished
 *
 *<code>
 * use Phalcon\Cache\Backend\Memory;
 * use Phalcon\Cache\Frontend\Data as FrontData;
 *
 * // Cache data
 * $frontCache = new FrontData();
 *
 * $cache = new Memory($frontCache);
 *
 * // Cache arbitrary data
 * $cache->save("my-data", [1, 2, 3, 4, 5]);
 *
 * // Get data
 * $data = $cache->get("my-data");
 *</code>
 **/

class Memory extends Backend {

    protected $_data;

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . keyName, $this->_lastKey = lastKey;
		}

		if ( !fetch cachedContent, $this->_data[lastKey] ) {
			return null;
		}

		if ( cachedContent === null ) {
			return null;
		}

		return $this->_frontend->afterRetrieve(cachedContent);
    }

    /***
	 * Stores cached content into the backend and stops the frontend
	 *
	 * @param string keyName
	 * @param string content
	 * @param int lifetime
	 * @param boolean stopBuffer
	 **/
    public function save($keyName  = null , $content  = null , $lifetime  = null , $stopBuffer  = true ) {

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

		$this->_data[lastKey] = preparedContent,
			isBuffering = frontend->isBuffering();

		if ( stopBuffer === true ) {
			frontend->stop();
		}

		if ( isBuffering === true ) {
			echo cachedContent;
		}

		$this->_started = false;

		return true;
    }

    /***
	 * Deletes a value from the cache by its key
	 *
	 * @param string keyName
	 * @return boolean
	 **/
    public function delete($keyName ) {

		$key = $this->_prefix . keyName,
			data = $this->_data;

		if ( isset($data[key]) ) {
			unset data[key];
			$this->_data = data;

			return true;
		}

		return false;
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

		$data = $this->_data;
		if ( gettype($data) != "array" ) {
			return [];
		}

		$keys = array_keys(data);
		foreach ( idx, $keys as $key ) {
			if ( !empty prefix && !starts_with(key, prefix) ) {
				unset keys[idx];
			}
		}

		return keys;
    }

    /***
	 * Checks if cache exists and it hasn't expired
	 *
	 * @param string|int keyName
	 * @param int lifetime
	 **/
    public function exists($keyName  = null , $lifetime  = null ) {

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . keyName;
		}

		if ( lastKey ) {
			if ( isset($this->_data[lastKey]) ) {
				return true;
			}
		}

		return false;
    }

    /***
	 * Increment of given $keyName by $value
	 *
	 * @param string keyName
	 **/
    public function increment($keyName  = null , $value  = 1 ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . keyName;
			$this->_lastKey = lastKey;
		}

		if ( !fetch cachedContent, $this->_data[lastKey] ) {
			return null;
		}

		if ( !cachedContent ) {
			return null;
		}

		$result = cachedContent + value;
		$this->_data[lastKey] = result;

		return result;
    }

    /***
	 * Decrement of $keyName by given $value
	 *
	 * @param string keyName
	 **/
    public function decrement($keyName  = null , $value  = 1 ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . keyName;
			$this->_lastKey = lastKey;
		}

		if ( !fetch cachedContent, $this->_data[lastKey] ) {
			return null;
		}

		if ( !cachedContent ) {
			return null;
		}

		$result = cachedContent - value;
		$this->_data[lastKey] = result;

		return result;
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {
		$this->_data = null;
		return true;
    }

    /***
	 * Required for interface \Serializable
	 **/
    public function serialize() {
			"frontend": $this->_frontend
		]);
    }

    /***
	 * Required for interface \Serializable
	 **/
    public function unserialize($data ) {

		$unserialized = unserialize(data);
		if ( gettype($unserialized) != "array" ) {
			throw new \Exception("Unserialized data must be an array");
		}

		$this->_frontend = unserialized["frontend"];
    }

}