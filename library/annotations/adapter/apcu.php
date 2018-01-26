<?php


namespace Phalcon\Annotations\Adapter;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Reflection;


/***
 * Phalcon\Annotations\Adapter\Apcu
 *
 * Stores the parsed annotations in APCu. This adapter is suitable for production
 *
 *<code>
 * use Phalcon\Annotations\Adapter\Apcu;
 *
 * $annotations = new Apcu();
 *</code>
 **/

class Apcu extends Adapter {

    protected $_prefix;

    protected $_ttl;

    /***
	 * Phalcon\Annotations\Adapter\Apcu constructor
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
	 * Reads parsed annotations from APCu
	 **/
    public function read($key ) {
		return apcu_fetch(strtolower("_PHAN" . $this->_prefix . key));
    }

    /***
	 * Writes parsed annotations to APCu
	 **/
    public function write($key , $data ) {
		return apcu_store(strtolower("_PHAN" . $this->_prefix . key), data, $this->_ttl);
    }

}