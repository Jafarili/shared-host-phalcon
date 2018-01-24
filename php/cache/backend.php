<?php


namespace Phalcon\Cache;

use Phalcon\Cache\FrontendInterface;


/***
 * Phalcon\Cache\Backend
 *
 * This class implements common functionality for backend adapters. A backend cache adapter may extend this class
 **/

abstract class Backend {

    protected $_frontend;

    protected $_options;

    protected $_prefix;

    protected $_lastKey;

    protected $_lastLifetime;

    protected $_fresh;

    protected $_started;

    /***
	 * Phalcon\Cache\Backend constructor
	 *
	 * @param \Phalcon\Cache\FrontendInterface frontend
	 * @param array options
	 **/
    public function __construct($frontend , $options  = null ) {

    }

    /***
	 * Starts a cache. The keyname allows to identify the created fragment
	 *
	 * @param   int|string keyName
	 * @param   int lifetime
	 * @return  mixed
	 **/
    public function start($keyName , $lifetime  = null ) {

    }

    /***
	 * Stops the frontend without store any cached content
	 **/
    public function stop($stopBuffer  = true ) {

    }

    /***
	 * Checks whether the last cache is fresh or cached
	 **/
    public function isFresh() {

    }

    /***
	 * Checks whether the cache has starting buffering or not
	 **/
    public function isStarted() {

    }

    /***
	 * Gets the last lifetime set
	 *
	 * @return int
	 **/
    public function getLifetime() {

    }

}