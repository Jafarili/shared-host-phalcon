<?php


namespace Phalcon\Annotations\Adapter;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Reflection;


/***
 * Phalcon\Annotations\Adapter\Xcache
 *
 * Stores the parsed annotations to XCache. This adapter is suitable for production
 *
 *<code>
 * $annotations = new \Phalcon\Annotations\Adapter\Xcache();
 *</code>
 **/

class Xcache extends Adapter {

    /***
	 * Reads parsed annotations from XCache
	 *
	 * @param string key
	 * @return \Phalcon\Annotations\Reflection
	 **/
    public function read($key ) {

    }

    /***
	 * Writes parsed annotations to XCache
	 **/
    public function write($key , $data ) {

    }

}