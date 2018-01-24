<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\Controller\BindModelInterface;
use Phalcon\Mvc\Model\Binder\BindableInterface;
use Phalcon\Cache\BackendInterface;


/***
 * Phalcon\Mvc\Model\Binding
 *
 * This is an class for binding models into params for handler
 **/

class Binder {

    /***
	 * Array for storing active bound models
	 *
	 * @var array
	 **/
    protected $boundModels;

    /***
	 * Cache object used for caching parameters for model binding
	 **/
    protected $cache;

    /***
	 * Internal cache for caching parameters for model binding during request
	 **/
    protected $internalCache;

    /***
	 * Array for original values
	 **/
    protected $originalValues;

    /***
	 * Phalcon\Mvc\Model\Binder constructor
	 **/
    public function __construct($cache  = null ) {

    }

    /***
	 * Gets cache instance
	 **/
    public function setCache($cache ) {

    }

    /***
	 * Sets cache instance
	 **/
    public function getCache() {

    }

    /***
	 * Bind models into params in proper handler
	 **/
    public function bindToHandler($handler , $params , $cacheKey , $methodName  = null ) {

    }

    /***
    * Find the model by param value.
    **/
    protected function findBoundModel($paramValue , $className ) {

    }

    /***
	 * Get params classes from cache by key
	 **/
    protected function getParamsFromCache($cacheKey ) {

    }

    /***
	 * Get modified params for handler using reflection
	 **/
    protected function getParamsFromReflection($handler , $params , $cacheKey , $methodName ) {

    }

}