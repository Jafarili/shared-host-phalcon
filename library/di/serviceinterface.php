<?php


namespace Phalcon\Di;



/***
 * Phalcon\Di\ServiceInterface
 *
 * Represents a service in the services container
 **/

interface ServiceInterface {

    /***
	 * Returns the service's name
	 *
	 * @param string
	 **/
    public function getName(); 

    /***
	 * Sets if the service is shared or not
	 **/
    public function setShared($shared ); 

    /***
	 * Check whether the service is shared or not
	 **/
    public function isShared(); 

    /***
	 * Set the service definition
	 *
	 * @param mixed definition
	 **/
    public function setDefinition($definition ); 

    /***
	 * Returns the service definition
	 *
	 * @return mixed
	 **/
    public function getDefinition(); 

    /***
	 * Resolves the service
	 *
	 * @param array parameters
	 * @param \Phalcon\DiInterface dependencyInjector
	 * @return mixed
	 **/
    public function resolve($parameters  = null , $dependencyInjector  = null ); 

    /***
	 * Changes a parameter in the definition without resolve the service
	 **/
    public function setParameter($position , $parameter ); 

    /***
	 * Restore the internal state of a service
	 **/
    public static function __set_state($attributes ); 

}