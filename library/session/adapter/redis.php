<?php


namespace Phalcon\Session\Adapter;

use Phalcon\Session\Adapter;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\None as FrontendNone;


/***
 * Phalcon\Session\Adapter\Redis
 *
 * This adapter store sessions in Redis
 *
 * <code>
 * use Phalcon\Session\Adapter\Redis;
 *
 * $session = new Redis(
 *     [
 *         "uniqueId"   => "my-private-app",
 *         "host"       => "localhost",
 *         "port"       => 6379,
 *         "auth"       => "foobared",
 *         "persistent" => false,
 *         "lifetime"   => 3600,
 *         "prefix"     => "my",
 *         "index"      => 1,
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

class Redis extends Adapter {

    protected $_redis;

    protected $_lifetime;

    /***
	 * Phalcon\Session\Adapter\Redis constructor
	 **/
    public function __construct($options ) {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function open() {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function close() {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function read($sessionId ) {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function write($sessionId , $data ) {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function destroy($sessionId  = null ) {

    }

    /***
	 * {@inheritdoc}
	 **/
    public function gc() {

    }

}