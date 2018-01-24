<?php


namespace Phalcon\Translate\Adapter;

use Phalcon\Translate\Exception;
use Phalcon\Translate\Adapter;


/***
 * Phalcon\Translate\Adapter\NativeArray
 *
 * Allows to define translation lists using PHP arrays
 **/

class NativeArray extends Adapter {

    protected $_translate;

    /***
	 * Phalcon\Translate\Adapter\NativeArray constructor
	 **/
    public function __construct($options ) {

    }

    /***
	 * Returns the translation related to the given key
	 **/
    public function query($index , $placeholders  = null ) {

    }

    /***
	 * Check whether is defined a translation key in the internal array
	 **/
    public function exists($index ) {

    }

}