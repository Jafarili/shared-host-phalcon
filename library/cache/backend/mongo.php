<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\Exception;
use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend\Mongo
 *
 * Allows to cache output fragments, PHP data or raw data to a MongoDb backend
 *
 *<code>
 * use Phalcon\Cache\Backend\Mongo;
 * use Phalcon\Cache\Frontend\Base64;
 *
 * // Cache data for 2 days
 * $frontCache = new Base64(
 *     [
 *         "lifetime" => 172800,
 *     ]
 * );
 *
 * // Create a MongoDB cache
 * $cache = new Mongo(
 *     $frontCache,
 *     [
 *         "server"     => "mongodb://localhost",
 *         "db"         => "caches",
 *         "collection" => "images",
 *     ]
 * );
 *
 * // Cache arbitrary data
 * $cache->save(
 *     "my-data",
 *     file_get_contents("some-image.jpg")
 * );
 *
 * // Get data
 * $data = $cache->get("my-data");
 *</code>
 **/

class Mongo extends Backend {

    protected $_collection;

    /***
	 * Phalcon\Cache\Backend\Mongo constructor
	 *
	 * @param \Phalcon\Cache\FrontendInterface frontend
	 * @param array options
	 **/
    public function __construct($frontend , $options  = null ) {
		if ( !isset options["mongo"] ) {
			if ( !isset options["server"] ) {
				throw new Exception("The parameter 'server' is required");
			}
		}

		if ( !isset options["db"] ) {
			throw new Exception("The parameter 'db' is required");
		}

		if ( !isset options["collection"] ) {
			throw new Exception("The parameter 'collection' is required");
		}

		parent::__construct(frontend, options);
    }

    /***
	 * Returns a MongoDb collection based on the backend parameters
	 *
	 * @return MongoCollection
	 **/
    protected final function _getCollection() {

		$mongoCollection = $this->_collection;
		if ( gettype($mongoCollection) != "object" ) {
			$options = $this->_options;

			/**
			 * If mongo is defined a valid Mongo object must be passed
			 */
			if ( fetch mongo, options["mongo"] ) {

				if ( gettype($mongo) != "object" ) {
					throw new Exception("The 'mongo' parameter must be a valid Mongo instance");
				}

			} else {

				/**
				 * Server must be defined otherwise
				 */
				$server = options["server"];
				if ( !server || gettype($server) != "string" ) {
					throw new Exception("The backend requires a valid MongoDB connection string");
				}

				$mongo = new \MongoClient(server);
			}

			/**
			 * Check if ( the database name is a string
			 */
			$database = options["db"];
			if ( !database || gettype($database) != "string" ) {
				throw new Exception("The backend requires a valid MongoDB db");
			}

			/**
			 * Retrieve the connection name
			 */
			$collection = options["collection"];
			if ( !collection || gettype($collection) != "string" ) {
				throw new Exception("The backend requires a valid MongoDB collection");
			}

			/**
			 * Make the connection and get the collection
			 */
			$mongoCollection = mongo->selectDb(database)->selectCollection(collection),
				this->_collection = mongoCollection;
		}

		return mongoCollection;
    }

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$conditions = [];
		$frontend = $this->_frontend;
		$prefixedKey = $this->_prefix . keyName;
		$this->_lastKey = prefixedKey;

		$conditions["key"] = prefixedKey;
		$conditions["time"] = ["$gt": time()];

		$document = $this->_getCollection()->findOne(conditions);
		if ( gettype($document) == "array" ) {
			if ( fetch cachedContent, document["data"] ) {
				if ( is_numeric(cachedContent) ) {
					return cachedContent;
				}
				return frontend->afterRetrieve(cachedContent);
			} else {
				throw new Exception("The cache is corrupt");
			}
		}

		return null;
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
			collection, timestamp, conditions, document, preparedContent,
			isBuffering, data, success;

		$conditions = [];
		$data = [];

		if ( keyName === null ) {
			$lastkey = $this->_lastKey;
		} else {
			$lastkey = $this->_prefix . keyName,
				this->_lastKey = lastkey;
		}

		if ( !lastkey ) {
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

		$collection = $this->_getCollection(),
			timestamp = time() + intval(ttl),
			conditions["key"] = lastkey,
			document = collection->findOne(conditions);

		if ( gettype($document) == "array" ) {
			$document["time"] = timestamp,
				document["data"] = preparedContent,
				success = collection->update(["_id": document["_id"]], document);
		} else {
			$data["key"] = lastkey,
				data["time"] = timestamp,
				data["data"] = preparedContent,
				success = collection->insert(data);
		}

		if ( !success ) {
			throw new Exception("Failed storing data in mongodb");
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
		this->_getCollection()->remove(["key": $this->_prefix . keyName]);

		if ( ((int) rand()) % 100 == 0 ) {
			this->gc();
		}

		return true;
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
		array keys = [], conditions = [];

		if ( !empty prefix ) {
			$conditions["key"] = new \MongoRegex("/^" . prefix . "/");
		}

		$conditions["time"] = ["$gt": time()];

		$collection = $this->_getCollection(),
			items = collection->find(conditions, ["key": 1]);

		foreach ( $iterator(items) as $item ) {
			foreach ( key, $item as $value ) {
				if ( key == "key" ) {
					$keys[] = value;
				}
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

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . keyName;
		}

		if ( lastKey ) {
			return $this->_getCollection()->count(["key": lastKey, "time": ["$gt": time()]]) > 0;
		}

		return false;
    }

    /***
	 * gc
	 * @return collection->remove(...)
	 **/
    public function gc() {
		return $this->_getCollection()->remove(["time": ["$lt": time()]]);
    }

    /***
	 * Increment of a given key by $value
	 *
	 * @param int|string keyName
	 **/
    public function increment($keyName , $value  = 1 ) {
			modif (iedTime,  cachedContent, incremented;

		$prefixedKey = $this->_prefix . keyName,
			this->_lastKey = prefixedKey;

		$document = $this->_getCollection()->findOne(["key": prefixedKey]);

		if ( !fetch modif (iedTime, document["time"] ) {
			throw new Exception("The cache is corrupted");
		}

		/**
		* The expiration is based on the column 'time'
		*/
		if ( time() < modif (iedTime ) {

			if ( !fetch cachedContent, document["data"] ) {
				throw new Exception("The cache is corrupted");
			}

			if ( is_numeric(cachedContent) ) {
				$incremented = cachedContent + value;
				this->save(prefixedKey, incremented);
				return incremented;
			}
		}

		return null;
    }

    /***
	 * Decrement of a given key by $value
	 *
	 * @param int|string $keyName
	 **/
    public function decrement($keyName , $value  = 1 ) {

		$prefixedKey = $this->_prefix . keyName,
			this->_lastKey = prefixedKey;

		$document = $this->_getCollection()->findOne(["key": prefixedKey]);

		if ( !fetch modif (iedTime, document["time"] ) {
			throw new Exception("The cache is corrupted");
		}

		/**
		* The expiration is based on the column 'time'
		*/
		if ( time() < modif (iedTime ) {

			if ( !fetch cachedContent, document["data"] ) {
				throw new Exception("The cache is corrupted");
			}

			if ( is_numeric(cachedContent) ) {
				$decremented = cachedContent - value;
				this->save(prefixedKey, decremented);
				return decremented;
			}
		}

		return null;
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {
		this->_getCollection()->remove();

		return true;
    }

}