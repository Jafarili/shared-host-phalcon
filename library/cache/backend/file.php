<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Cache\Exception;
use Phalcon\Cache\Backend;
use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend\File
 *
 * Allows to cache output fragments using a file backend
 *
 *<code>
 * use Phalcon\Cache\Backend\File;
 * use Phalcon\Cache\Frontend\Output as FrontOutput;
 *
 * // Cache the file for 2 days
 * $frontendOptions = [
 *     "lifetime" => 172800,
 * ];
 *
 * // Create an output cache
 * $frontCache = FrontOutput($frontOptions);
 *
 * // Set the cache directory
 * $backendOptions = [
 *     "cacheDir" => "../app/cache/",
 * ];
 *
 * // Create the File backend
 * $cache = new File($frontCache, $backendOptions);
 *
 * $content = $cache->start("my-cache");
 *
 * if ($content === null) {
 *     echo "<h1>", time(), "</h1>";
 *
 *     $cache->save();
 * } else {
 *     echo $content;
 * }
 *</code>
 **/

class File extends Backend {

    /***
	 * Default to false for backwards compatibility
	 *
	 * @var boolean
	 **/
    private $_useSafeKey;

    /***
	 * Phalcon\Cache\Backend\File constructor
	 **/
    public function __construct($frontend , $options ) {

		if ( !isset options["cacheDir"] ) {
			throw new Exception("Cache directory must be specif (ied with the option cacheDir");
		}

		if ( fetch safekey, options["safekey"] ) {
			if ( gettype($safekey) !== "boolean" ) {
				throw new Exception("safekey option should be a boolean.");
			}

			$this->_useSafeKey = safekey;
		}

		// added to avoid having unsafe filesystem characters in the prefix
		if ( fetch prefix, options["prefix"] ) {
			if ( $this->_useSafeKey && preg_match("/[^a-zA-Z0-9_.-]+/", prefix) ) {
				throw new Exception("FileCache prefix should only use alphanumeric characters.");
			}
		}

		parent::__construct(frontend, options);
    }

    /***
	 * Returns a cached content
	 **/
    public function get($keyName , $lifetime  = null ) {

		$prefixedKey =  $this->_prefix . $this->getKey(keyName);
		$this->_lastKey = prefixedKey;

		if ( !fetch cacheDir, $this->_options["cacheDir"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		$cacheFile = cacheDir . prefixedKey;

		if ( file_exists(cacheFile) === true ) {

			$frontend = $this->_frontend;

			/**
			 * Take the lif (etime from the frontend or read it from the set in start()
			 */
			if ( !lif (etime ) {
				$lastLif (etime = $this->_lastLif (etime;
				if ( !lastLif (etime ) {
					$ttl = (int) frontend->getLif (eTime();
				} else {
					$ttl = (int) lastLif (etime;
				}
			} else {
				$ttl = (int) lif (etime;
			}

			clearstatcache(true, cacheFile);
			$modif (iedTime = (int) filemtime(cacheFile);

			/**
			 * Check if ( the file has expired
			 * The content is only retrieved if ( the content has not expired
			 */
			if ( modif (iedTime + ttl > time() ) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents(cacheFile);
				if ( cachedContent === false ) {
					throw new Exception("Cache file ". cacheFile. " could not be opened");
				}

				if ( is_numeric(cachedContent) ) {
					return cachedContent;
				} else {
					/**
					 * Use the frontend to process the content of the cache
					 */
					$ret = frontend->afterRetrieve(cachedContent);
					return ret;
				}
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

		if ( keyName === null ) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $this->getKey(keyName),
				this->_lastKey = lastKey;
		}

		if ( !lastKey ) {
			throw new Exception("Cache must be started first");
		}

		$frontend = $this->_frontend;

		if ( !fetch cacheDir, $this->_options["cacheDir"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		$cacheFile = cacheDir . lastKey;

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
		 * We use file_put_contents to respect open-base-dir directive
		 */
		$status = file_put_contents(cacheFile, preparedContent);

		if ( status === false ) {
			throw new Exception("Cache file ". cacheFile . " could not be written");
		}

		$isBuffering = frontend->isBuffering();

		if ( stopBuffer === true ) {
			frontend->stop();
		}

		if ( isBuffering === true ) {
			echo cachedContent;
		}

		$this->_started = false;

		return status !== false;
    }

    /***
	 * Deletes a value from the cache by its key
	 *
	 * @param int|string keyName
	 **/
    public function delete($keyName ) {

		if ( !fetch cacheDir, $this->_options["cacheDir"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		$cacheFile = cacheDir . $this->_prefix . $this->getKey(keyName);
		if ( file_exists(cacheFile) ) {
			return unlink(cacheFile);
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
		array keys = [];

		if ( !fetch cacheDir, $this->_options["cacheDir"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		if ( !empty prefix ) {
			$prefixedKey = $this->_prefix . $this->getKey(prefix);
		}

		/**
		 * We use a directory iterator to traverse the cache dir directory
		 */
		foreach ( $iterator(new as $item \DirectoryIterator(cacheDir)) ) {
			if ( likely item->isDir() === false ) {
				$key = item->getFileName();
				if ( !empty prefix ) {
					if ( starts_with(key, prefixedKey) ) {
						$keys[] = key;
					}
				} else {
					$keys[] = key;
				}
			}
		}

		return keys;
    }

    /***
	 * Checks if cache exists and it isn't expired
	 *
	 * @param string|int keyName
	 * @param int lifetime
	 **/
    public function exists($keyName  = null , $lifetime  = null ) {

		if ( !keyName ) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = prefix . $this->getKey(keyName);
		}

		if ( lastKey ) {

			$cacheFile = $this->_options["cacheDir"] . lastKey;

			if ( file_exists(cacheFile) ) {

				/**
				 * Check if ( the file has expired
				 */
				if ( !lif (etime ) {
					$ttl = (int) $this->_frontend->getLif (eTime();
				} else {
					$ttl = (int) lif (etime;
				}

				clearstatcache(true, cacheFile);
				$modif (iedTime = (int) filemtime(cacheFile);

				if ( modif (iedTime + ttl > time() ) {
					return true;
				}
			}
		}

		return false;
    }

    /***
	 * Increment of a given key, by number $value
	 *
	 * @param  string|int keyName
	 **/
    public function increment($keyName  = null , $value  = 1 ) {
			cachedContent, result, modif (iedTime;

		$prefixedKey = $this->_prefix . $this->getKey(keyName),
			this->_lastKey = prefixedKey,
			cacheFile = $this->_options["cacheDir"] . prefixedKey;

		if ( file_exists(cacheFile) ) {

			$frontend = $this->_frontend;

			/**
			 * Take the lif (etime from the frontend or read it from the set in start()
			 */
			$lif (etime = $this->_lastLif (etime;
			if ( !lif (etime ) {
				$ttl = frontend->getLif (eTime();
			} else {
				$ttl = lif (etime;
			}

			clearstatcache(true, cacheFile);
			$modif (iedTime = (int) filemtime(cacheFile);

			/**
			 * Check if ( the file has expired
			 * The content is only retrieved if ( the content has not expired
			 */
			if ( modif (iedTime + ttl > time() ) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents(cacheFile);

				if ( cachedContent === false ) {
					throw new Exception("Cache file " . cacheFile . " could not be opened");
				}

				if ( is_numeric(cachedContent) ) {

					$result = cachedContent + value;
					if ( file_put_contents(cacheFile, result) === false ) {
						throw new Exception("Cache directory could not be written");
					}

					return result;
				}
			}
		}

		return null;
    }

    /***
	 * Decrement of a given key, by number $value
	 *
	 * @param string|int keyName
	 **/
    public function decrement($keyName  = null , $value  = 1 ) {

		$prefixedKey = $this->_prefix . $this->getKey(keyName),
			this->_lastKey = prefixedKey,
			cacheFile = $this->_options["cacheDir"] . prefixedKey;

		if ( file_exists(cacheFile) ) {

			/**
			 * Take the lif (etime from the frontend or read it from the set in start()
			 */
			$lif (etime = $this->_lastLif (etime;
			if ( !lif (etime ) {
				$ttl = $this->_frontend->getLif (eTime();
			} else {
				$ttl = lif (etime;
			}

			clearstatcache(true, cacheFile);
			$modif (iedTime = (int) filemtime(cacheFile);

			/**
			 * Check if ( the file has expired
			 * The content is only retrieved if ( the content has not expired
			 */
			if ( modif (iedTime + ttl > time() ) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents(cacheFile);

				if ( cachedContent === false ) {
					throw new Exception("Cache file " . cacheFile . " could not be opened");
				}

				if ( is_numeric(cachedContent) ) {

					$result = cachedContent - value;
					if ( file_put_contents(cacheFile, result) === false ) {
						throw new Exception("Cache directory can't be written");
					}

					return result;
				}
			}
		}

		return null;
    }

    /***
	 * Immediately invalidates all existing items.
	 **/
    public function flush() {

		$prefix = $this->_prefix;

		if ( !fetch cacheDir, $this->_options["cacheDir"] ) {
			throw new Exception("Unexpected inconsistency in options");
		}

		foreach ( $iterator(new as $item \DirectoryIterator(cacheDir)) ) {

			if ( likely item->isFile() == true ) {
				$key = item->getFileName(),
					cacheFile = item->getPathName();

				if ( empty prefix || starts_with(key, prefix) ) {
					if (  !unlink(cacheFile) ) {
						return false;
					}
				}
			}
		}

		return true;
    }

    /***
	 * Return a file-system safe identifier for a given key
	 **/
    public function getKey($key ) {
		if ( $this->_useSafeKey === true ) {
			return md5(key);
		}

		return key;
    }

    /***
	 * Set whether to use the safekey or not
	 **/
    public function useSafeKey($useSafeKey ) {
		$this->_useSafeKey = useSafeKey;

		return this;
    }

}