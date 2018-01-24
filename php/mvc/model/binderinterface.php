<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Cache\BackendInterface;


/***
 * Phalcon\Mvc\Model\BinderInterface
 *
 * Interface for Phalcon\Mvc\Model\Binder
 **/

interface BinderInterface {

    /***
	 * Gets active bound models
	 **/
    public function getBoundModels(); 

    /***
	 * Gets cache instance
	 **/
    public function getCache(); 

    /***
	 * Sets cache instance
	 **/
    public function setCache($cache ); 

    /***
	 * Bind models into params in proper handler
	 **/
    public function bindToHandler($handler , $params , $cacheKey , $methodName  = null ); 

}