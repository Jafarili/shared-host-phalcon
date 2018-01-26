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
		$serialized = xcache_get(strtolower("_PHAN" . key));
		if ( gettype($serialized) == "string" ) {
			$data = unserialize(serialized);
			if ( gettype($data) == "object" ) {
				return data;
			}
		}
		return false;
    }

    /***
	 * Writes parsed annotations to XCache
	 **/
    public function write($key , $data ) {
		xcache_set(strtolower("_PHAN" . key), serialize(data));
    }

}