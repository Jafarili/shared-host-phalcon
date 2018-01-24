<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Cache\Backend\Memcache;
use Phalcon\Cache\Frontend\Data as FrontendData;


/***
 * Phalcon\Mvc\Model\MetaData\Memcache
 *
 * Stores model meta-data in the Memcache.
 *
 * By default meta-data is stored for 48 hours (172800 seconds)
 *
 *<code>
 * $metaData = new Phalcon\Mvc\Model\Metadata\Memcache(
 *     [
 *         "prefix"     => "my-app-id",
 *         "lifetime"   => 86400,
 *         "host"       => "localhost",
 *         "port"       => 11211,
 *         "persistent" => false,
 *     ]
 * );
 *</code>
 **/

class Memcache extends MetaData {

    protected $_ttl;

    protected $_memcache;

    protected $_metaData;

    /***
	 * Phalcon\Mvc\Model\MetaData\Memcache constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {

    }

    /***
	 * Reads metadata from Memcache
	 **/
    public function read($key ) {

    }

    /***
	 * Writes the metadata to Memcache
	 **/
    public function write($key , $data ) {

    }

    /***
	 * Flush Memcache data and resets internal meta-data in order to regenerate it
	 **/
    public function reset() {

    }

}