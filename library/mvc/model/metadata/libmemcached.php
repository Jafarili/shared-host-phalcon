<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\MetaData\Libmemcached
 *
 * Stores model meta-data in the Memcache.
 *
 * By default meta-data is stored for 48 hours (172800 seconds)
 *
 *<code>
 * $metaData = new Phalcon\Mvc\Model\Metadata\Libmemcached(
 *     [
 *         "servers" => [
 *             [
 *                 "host"   => "localhost",
 *                 "port"   => 11211,
 *                 "weight" => 1,
 *             ],
 *         ],
 *         "client" => [
 *             Memcached::OPT_HASH       => Memcached::HASH_MD5,
 *             Memcached::OPT_PREFIX_KEY => "prefix.",
 *         ],
 *         "lifetime" => 3600,
 *         "prefix"   => "my_",
 *     ]
 * );
 *</code>
 **/

class Libmemcached extends MetaData {

    protected $_ttl;

    protected $_memcache;

    protected $_metaData;

    /***
	 * Phalcon\Mvc\Model\MetaData\Libmemcached constructor
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