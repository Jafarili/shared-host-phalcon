<?php


namespace Phalcon\Annotations\Adapter;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Reflection;


/***
 * Phalcon\Annotations\Adapter\Memory
 *
 * Stores the parsed annotations in memory. This adapter is the suitable development/testing
 **/

class Memory extends Adapter {

    /***
	 * Data
	 * @var mixed
	 **/
    protected $_data;

    /***
	 * Reads parsed annotations from memory
	 **/
    public function read($key ) {

    }

    /***
	 * Writes parsed annotations to memory
	 **/
    public function write($key , $data ) {

    }

}