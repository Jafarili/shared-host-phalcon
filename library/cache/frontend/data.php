<?php


namespace Phalcon\Cache\Frontend;

use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Frontend\Data
 *
 * Allows to cache native PHP data in a serialized form
 *
 *<code>
 * use Phalcon\Cache\Backend\File;
 * use Phalcon\Cache\Frontend\Data;
 *
 * // Cache the files for 2 days using a Data frontend
 * $frontCache = new Data(
 *     [
 *         "lifetime" => 172800,
 *     ]
 * );
 *
 * // Create the component that will cache "Data" to a 'File' backend
 * // Set the cache file directory - important to keep the '/' at the end of
 * // of the value for the folder
 * $cache = new File(
 *     $frontCache,
 *     [
 *         "cacheDir" => "../app/cache/",
 *     ]
 * );
 *
 * $cacheKey = "robots_order_id.cache";
 *
 * // Try to get cached records
 * $robots = $cache->get($cacheKey);
 *
 * if ($robots === null) {
 *     // $robots is null due to cache expiration or data does not exist
 *     // Make the database call and populate the variable
 *     $robots = Robots::find(
 *         [
 *             "order" => "id",
 *         ]
 *     );
 *
 *     // Store it in the cache
 *     $cache->save($cacheKey, $robots);
 * }
 *
 * // Use $robots :)
 * foreach ($robots as $robot) {
 *     echo $robot->name, "\n";
 * }
 *</code>
 **/

class Data {

    protected $_frontendOptions;

    /***
	 * Phalcon\Cache\Frontend\Data constructor
	 *
	 * @param array frontendOptions
	 **/
    public function __construct($frontendOptions  = null ) {
		$this->_frontendOptions = frontendOptions;
    }

    /***
	 * Returns the cache lifetime
	 **/
    public function getLifetime() {
		$options = $this->_frontendOptions;
		if ( gettype($options) == "array" ) {
			if ( fetch lif (etime, options["lif (etime"] ) {
				return lif (etime;
			}
		}
		return 1;
    }

    /***
	 * Check whether if frontend is buffering output
	 **/
    public function isBuffering() {
		return false;
    }

    /***
	 * Starts output frontend. Actually, does nothing
	 **/
    public function start() {

    }

    /***
	 * Returns output cached content
	 *
	 * @return string
	 **/
    public function getContent() {
		return null;
    }

    /***
	 * Stops output frontend
	 **/
    public function stop() {

    }

    /***
	 * Serializes data before storing them
	 **/
    public function beforeStore($data ) {
		return serialize(data);
    }

    /***
	 * Unserializes data after retrieval
	 **/
    public function afterRetrieve($data ) {
		if ( is_numeric(data) ) {
			return data;
		}

		// do not unserialize empty string, null, false, etc
		if ( empty data ) {
			return data;
		}

		return unserialize(data);
    }

}