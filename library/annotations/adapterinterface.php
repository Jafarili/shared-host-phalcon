<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\Reflection;
use Phalcon\Annotations\Collection;
use Phalcon\Annotations\ReaderInterface;


/***
 * Phalcon\Annotations\AdapterInterface
 *
 * This interface must be implemented by adapters in Phalcon\Annotations
 **/

interface AdapterInterface {

    /***
	 * Sets the annotations parser
	 **/
    public function setReader($reader ); 

    /***
	 * Returns the annotation reader
	 **/
    public function getReader(); 

    /***
	 * Parses or retrieves all the annotations found in a class
	 *
	 * @param string|object className
     **/
    public function get($className ); 

    /***
	 * Returns the annotations found in all the class' methods
	 **/
    public function getMethods($className ); 

    /***
	 * Returns the annotations found in a specific method
	 **/
    public function getMethod($className , $methodName ); 

    /***
	 * Returns the annotations found in all the class' methods
	 **/
    public function getProperties($className ); 

    /***
	 * Returns the annotations found in a specific property
	 **/
    public function getProperty($className , $propertyName ); 

}