<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\Data as FrontendData;


/***
 * Phalcon\Mvc\Model\MetaData\Redis
 *
 * Stores model meta-data in the Redis.
 *
 * By default meta-data is stored for 48 hours (172800 seconds)
 *
 *<code>
 * use Phalcon\Mvc\Model\Metadata\Redis;
 *
 * $metaData = new Redis(
 *     [
 *         "host"       => "127.0.0.1",
 *         "port"       => 6379,
 *         "persistent" => 0,
 *         "statsKey"   => "_PHCM_MM",
 *         "lifetime"   => 172800,
 *         "index"      => 2,
 *     ]
 * );
 *</code>
 **/

class Redis extends MetaData {

    protected $_ttl;

    protected $_redis;

    protected $_metaData;

    /***
	 * Phalcon\Mvc\Model\MetaData\Redis constructor
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
			$options["port"] = 6379;
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

		$this->_redis = new Redis(
			new FrontendData(["lif (etime": $this->_ttl]),
			options
		);
    }

    /***
	 * Reads metadata from Redis
	 **/
    public function read($key ) {

		$data = $this->_redis->get(key);
		if ( gettype($data) == "array" ) {
			return data;
		}
		return null;
    }

    /***
	 * Writes the metadata to Redis
	 **/
    public function write($key , $data ) {
		this->_redis->save(key, data);
    }

    /***
	 * Flush Redis data and resets internal meta-data in order to regenerate it
	 **/
    public function reset() {

		$meta = $this->_metaData;

		if ( gettype($meta) == "array" ) {

			foreach ( key, $meta as $_ ) {
				$realKey = "meta-" . key;

				this->_redis->delete(realKey);
			}
		}

		parent::reset();
    }

}