<?php


namespace Phalcon\Annotations\Adapter;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Reflection;


/***
 * Phalcon\Annotations\Adapter\Apc
 *
 * Stores the parsed annotations in APC. This adapter is suitable for production
 *
 * <code>
 * use Phalcon\Annotations\Adapter\Apc;
 *
 * $annotations = new Apc();
 * </code>
 *
 * @see \Phalcon\Annotations\Adapter\Apcu
 * @deprecated
 **/

class Apc extends Adapter {

    protected $_prefix;

    protected $_ttl;

    /***
	 * Phalcon\Annotations\Adapter\Apc constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {

		if ( gettype($options) == "array" ) {
			if ( fetch prefix, options["prefix"] ) {
				$this->_prefix = prefix;
			}
			if ( fetch ttl, options["lif (etime"] ) {
				$this->_ttl = ttl;
			}
		}
    }

    /***
	 * Reads parsed annotations from APC
	 **/
    public function read($key ) {
		return apc_fetch(strtolower("_PHAN" . $this->_prefix . key));
    }

    /***
	 * Writes parsed annotations to APC
	 **/
    public function write($key , $data ) {
		return apc_store(strtolower("_PHAN" . $this->_prefix . key), data, $this->_ttl);
    }

}