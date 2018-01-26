<?php


namespace Phalcon\Cache\Frontend;

use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Frontend\Output
 *
 * Allows to cache output fragments captured with ob_* functions
 *
*<code>
* use Phalcon\Tag;
* use Phalcon\Cache\Backend\File;
* use Phalcon\Cache\Frontend\Output;
*
* // Create an Output frontend. Cache the files for 2 days
* $frontCache = new Output(
*     [
*         "lifetime" => 172800,
*     ]
* );
*
* // Create the component that will cache from the "Output" to a "File" backend
* // Set the cache file directory - it's important to keep the "/" at the end of
* // the value for the folder
* $cache = new File(
*     $frontCache,
*     [
*         "cacheDir" => "../app/cache/",
*     ]
* );
*
* // Get/Set the cache file to ../app/cache/my-cache.html
* $content = $cache->start("my-cache.html");
*
* // If $content is null then the content will be generated for the cache
* if (null === $content) {
*     // Print date and time
*     echo date("r");
*
*     // Generate a link to the sign-up action
*     echo Tag::linkTo(
*         [
*             "user/signup",
*             "Sign Up",
*             "class" => "signup-button",
*         ]
*     );
*
*     // Store the output into the cache file
*     $cache->save();
* } else {
*     // Echo the cached output
*     echo $content;
* }
*</code>
 **/

class Output {

    protected $_buffering;

    protected $_frontendOptions;

    /***
	 * Phalcon\Cache\Frontend\Output constructor
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
		return $this->_buffering;
    }

    /***
	 * Starts output frontend. Currently, does nothing
	 **/
    public function start() {
		$this->_buffering = true;
		ob_start();
    }

    /***
	 * Returns output cached content
	 *
	 * @return string
	 **/
    public function getContent() {
		if ( $this->_buffering ) {
			return ob_get_contents();
		}

		return null;
    }

    /***
	 * Stops output frontend
	 **/
    public function stop() {
		if ( $this->_buffering ) {
			ob_end_clean();
		}

		$this->_buffering = false;
    }

    /***
	 * Serializes data before storing them
	 **/
    public function beforeStore($data ) {
		return data;
    }

    /***
	 * Unserializes data after retrieval
	 **/
    public function afterRetrieve($data ) {
		return data;
    }

}