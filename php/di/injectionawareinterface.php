<?php


namespace Phalcon\Di;

use Phalcon\DiInterface;


/***
 * Phalcon\Di\InjectionAwareInterface
 *
 * This interface must be implemented in those classes that uses internally the Phalcon\Di that creates them
 **/

interface InjectionAwareInterface {

    /***
	 * Sets the dependency injector
	 **/
    public function setDI($dependencyInjector ); 

    /***
	 * Returns the internal dependency injector
	 **/
    public function getDI(); 

}