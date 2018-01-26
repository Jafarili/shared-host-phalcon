<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\Collection;


/***
 * Phalcon\Annotations\Reflection
 *
 * Allows to manipulate the annotations reflection in an OO manner
 *
 *<code>
 * use Phalcon\Annotations\Reader;
 * use Phalcon\Annotations\Reflection;
 *
 * // Parse the annotations in a class
 * $reader = new Reader();
 * $parsing = $reader->parse("MyComponent");
 *
 * // Create the reflection
 * $reflection = new Reflection($parsing);
 *
 * // Get the annotations in the class docblock
 * $classAnnotations = $reflection->getClassAnnotations();
 *</code>
 **/

class Reflection {

    protected $_reflectionData;

    protected $_classAnnotations;

    protected $_methodAnnotations;

    protected $_propertyAnnotations;

    /***
	 * Phalcon\Annotations\Reflection constructor
	 *
	 * @param array reflectionData
	 **/
    public function __construct($reflectionData  = null ) {
		if ( gettype($reflectionData) == "array" ) {
			$this->_reflectionData = reflectionData;
		}
    }

    /***
	 * Returns the annotations found in the class docblock
	 **/
    public function getClassAnnotations() {

		$annotations = $this->_classAnnotations;
		if ( gettype($annotations) != "object" ) {
			if ( fetch reflectionClass, $this->_reflectionData["class"] ) {
				$collection = new Collection(reflectionClass),
					this->_classAnnotations = collection;
				return collection;
			}
			$this->_classAnnotations = false;
			return false;
		}
		return annotations;
    }

    /***
	 * Returns the annotations found in the methods' docblocks
	 **/
    public function getMethodsAnnotations() {
			collections, methodName, reflectionMethod;

		$annotations = $this->_methodAnnotations;
		if ( gettype($annotations) != "object" ) {

			if ( fetch reflectionMethods, $this->_reflectionData["methods"] ) {
				if ( count(reflectionMethods) ) {
					$collections = [];
					foreach ( methodName, $reflectionMethods as $reflectionMethod ) {
						$collections[methodName] = new Collection(reflectionMethod);
					}
					$this->_methodAnnotations = collections;
					return collections;
				}
			}

			$this->_methodAnnotations = false;
			return false;
		}
		return annotations;
    }

    /***
	 * Returns the annotations found in the properties' docblocks
	 **/
    public function getPropertiesAnnotations() {
			collections, property, reflectionProperty;

		$annotations = $this->_propertyAnnotations;
		if ( gettype($annotations) != "object" ) {
			if ( fetch reflectionProperties, $this->_reflectionData["properties"] ) {
				if ( count(reflectionProperties) ) {
					$collections = [];
					foreach ( property, $reflectionProperties as $reflectionProperty ) {
						$collections[property] = new Collection(reflectionProperty);
					}
					$this->_propertyAnnotations = collections;
					return collections;
				}
			}
			$this->_propertyAnnotations = false;
			return false;
		}

		return annotations;
    }

    /***
	 * Returns the raw parsing intermediate definitions used to construct the reflection
	 *
	 * @return array
	 **/
    public function getReflectionData() {
		return $this->_reflectionData;
    }

    /***
	 * Restores the state of a Phalcon\Annotations\Reflection variable export
	 *
	 * @return array data
	 **/
    public static function __set_state($data ) {

		if ( gettype($data) == "array" ) {
			/**
			 * Check for ( a '_reflectionData' in the array to build the Reflection
			 */
			if ( fetch reflectionData, data["_reflectionData"] ) {
				return new self(reflectionData);
			}
		}

		return new self();
    }

}