<?php


namespace Phalcon\Mvc\Micro;

use Phalcon\Mvc\Model\BinderInterface;


/***
 * Phalcon\Mvc\Micro\LazyLoader
 *
 * Lazy-Load of handlers for Mvc\Micro using auto-loading
 **/

class LazyLoader {

    protected $_handler;

    protected $_modelBinder;

    protected $_definition;

    /***
	 * Phalcon\Mvc\Micro\LazyLoader constructor
	 **/
    public function __construct($definition ) {

    }

    /***
	 * Initializes the internal handler, calling functions on it
	 *
	 * @param  string method
	 * @param  array arguments
	 * @return mixed
	 **/
    public function __call($method , $arguments ) {

    }

    /***
	 * Calling __call method
	 *
	 * @param  string method
	 * @param  array arguments
	 * @return mixed
	 **/
    public function callMethod($method , $arguments , $modelBinder  = null ) {

    }

}