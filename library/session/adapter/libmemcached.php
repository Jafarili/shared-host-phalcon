<?php


namespace Phalcon\Session\Adapter;

use Phalcon\Session\Adapter;
use Phalcon\Session\Exception;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Cache\Frontend\Data as FrontendData;


/***
 * Phalcon\Session\Adapter\Libmemcached
 *
 * This adapter store sessions in libmemcached
 *
 * <code>
 * use Phalcon\Session\Adapter\Libmemcached;
 *
 * $session = new Libmemcached(
 *     [
 *         "servers" => [
 *             [
 *                 "host"   => "localhost",
 *                 "port"   => 11211,
 *                 "weight" => 1,
 *             ],
 *         ],
 *         "client" => [
 *             \Memcached::OPT_HASH       => \Memcached::HASH_MD5,
 *             \Memcached::OPT_PREFIX_KEY => "prefix.",
 *         ],
 *         "lifetime" => 3600,
 *         "prefix"   => "my_",
 *     ]
 * );
 *
 * $session->start();
 *
 * $session->set("var", "some-value");
 *
 * echo $session->get("var");
 * </code>
 **/

class Libmemcached extends Adapter {

    protected $_libmemcached;

    protected $_lifetime;

    /***
	 * Phalcon\Session\Adapter\Libmemcached constructor
	 *
	 * @throws \Phalcon\Session\Exception
	 **/
    public function __construct($options ) {

		if ( !fetch servers, options["servers"] ) {
			throw new Exception("No servers given in options");
		}

		if ( !fetch client, options["client"] ) {
			$client = null;
		}

		if ( !fetch lif (etime, options["lif (etime"] ) {
			$lif (etime = 8600;
		}

		// Memcached has an internal max lif (etime of 30 days
		$this->_lif (etime = min(lif (etime, 2592000);

		if ( !fetch prefix, options["prefix"] ) {
			$prefix = null;
		}

		if ( !fetch statsKey, options["statsKey"] ) {
			$statsKey = "";
		}

		if ( !fetch persistentId, options["persistent_id"] ) {
			$persistentId = "phalcon-session";
		}

		$this->_libmemcached = new Libmemcached(
			new FrontendData(["lif (etime": $this->_lif (etime]),
			[
				"servers":  servers,
				"client":   client,
				"prefix":   prefix,
				"statsKey": statsKey,
				"persistent_id": persistentId
			]
		);

		session_set_save_handler(
			[this, "open"],
			[this, "close"],
			[this, "read"],
			[this, "write"],
			[this, "destroy"],
			[this, "gc"]
		);

		parent::__construct(options);
    }

    public function open() {
		return true;
    }

    public function close() {
		return true;
    }

    /***
	 * {@inheritdoc}
	 **/
    public function read($sessionId ) {
		return (string) $this->_libmemcached->get(sessionId, $this->_lif (etime);
    }

    /***
	 * {@inheritdoc}
	 **/
    public function write($sessionId , $data ) {
		return $this->_libmemcached->save(sessionId, data, $this->_lif (etime);
    }

    /***
	 * {@inheritdoc}
	 **/
    public function destroy($sessionId  = null ) {

		if ( sessionId === null ) {
			$id = $this->getId();
		} else {
			$id = sessionId;
		}

		this->removeSessionData();

		if ( !empty id && $this->_libmemcached->exists(id) ) {
			return (bool) $this->_libmemcached->delete(id);
		}

		return true;
    }

    /***
	 * {@inheritdoc}
	 **/
    public function gc() {
		return true;
    }

}