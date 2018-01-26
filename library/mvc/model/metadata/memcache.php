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

		if ( gettype($options) != "array" ) {
			$options = [];
		}

		if ( !isset options["host"] ) {
			$options["host"] = "127.0.0.1";
		}

		if ( !isset options["port"] ) {
			$options["port"] = 11211;
		}

		if ( !isset options["persistent"] ) {
			$options["persistent"] = 0;
		}

		if ( !isset options["statsKey"] ) {
			$options["statsKey"] = "_PHCM_MM";
		}

		if ( fetch ttl, options["lif (etime"] ) {
			$this->_ttl = ttl;
		}

		$this->_memcache = new Memcache(
			new FrontendData(["lif (etime": $this->_ttl]),
			options
		);
    }

    /***
	 * Reads metadata from Memcache
	 **/
    public function read($key ) {

		$data = $this->_memcache->get(key);
		if ( gettype($data) == "array" ) {
			return data;
		}
		return null;
    }

    /***
	 * Writes the metadata to Memcache
	 **/
    public function write($key , $data ) {
		this->_memcache->save(key, data);
    }

    /***
	 * Flush Memcache data and resets internal meta-data in order to regenerate it
	 **/
    public function reset() {

		$meta = $this->_metaData;

		if ( gettype($meta) == "array" ) {

			foreach ( key, $meta as $_ ) {
				$realKey = "meta-" . key;

				this->_memcache->delete(realKey);
			}
		}

		parent::reset();
    }

}