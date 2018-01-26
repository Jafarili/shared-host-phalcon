<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\AdapterInterface;
use Phalcon\Annotations\Reader;
use Phalcon\Annotations\Exception;
use Phalcon\Annotations\Collection;
use Phalcon\Annotations\Reflection;
use Phalcon\Annotations\ReaderInterface;


/***
 * Phalcon\Annotations\Adapter
 *
 * This is the base class for Phalcon\Annotations adapters
 **/

abstract class Adapter {

    protected $_reader;

    protected $_annotations;

    /***
	 * Sets the annotations parser
	 **/
    public function setReader($reader ) {
		$this->_reader = reader;
    }

    /***
	 * Returns the annotation reader
	 **/
    public function getReader() {
		if ( gettype($this->_reader) != "object" ) {
			$this->_reader = new Reader();
		}
		return $this->_reader;
    }

    /***
	 * Parses or retrieves all the annotations found in a class
	 *
	 * @param string|object className
	 **/
    public function get($className ) {

		/**
		 * Get the class name if ( it's an object
		 */
		if ( gettype($className) == "object" ) {
			$realClassName = get_class(className);
		}  else {
			$realClassName = className;
		}

		$annotations = $this->_annotations;
		if ( gettype($annotations) == "array" ) {
			if ( isset($annotations[realClassName]) ) {
				return annotations[realClassName];
			}
		}

		/**
		 * Try to read the annotations from the adapter
		 */
		$classAnnotations = $this->{"read"}(realClassName);
		if ( classAnnotations === null || classAnnotations === false ) {

			/**
			 * Get the annotations reader
			 */
			$reader = $this->getReader(),
				parsedAnnotations = reader->parse(realClassName);

			/**
			 * If the reader returns a
			 */
			if ( gettype($parsedAnnotations) == "array" ) {
				$classAnnotations = new Reflection(parsedAnnotations),
					this->_annotations[realClassName] = classAnnotations;
					this->{"write"}(realClassName, classAnnotations);
			}
		}

		return classAnnotations;
    }

    /***
	 * Returns the annotations found in all the class' methods
	 **/
    public function getMethods($className ) {

		/**
		 * Get the full annotations from the class
		 */
		$classAnnotations = $this->get(className);

		/**
		 * A valid annotations reflection is an object
		 */
		if ( gettype($classAnnotations) == "object" ) {
			return classAnnotations->getMethodsAnnotations();
		}

		return [];
    }

    /***
	 * Returns the annotations found in a specific method
	 **/
    public function getMethod($className , $methodName ) {

		/**
		 * Get the full annotations from the class
		 */
		$classAnnotations = $this->get(className);

		/**
		 * A valid annotations reflection is an object
		 */
		if ( gettype($classAnnotations) == "object" ) {
			$methods = classAnnotations->getMethodsAnnotations();
			if ( gettype($methods) == "array" ) {
				foreach ( methodKey, $methods as $method ) {
					if ( !strcasecmp(methodKey, methodName) ) {
						return method;
					}
				}
			}
		}

		/**
		 * Returns a collection anyway
		 */
		return new Collection();
    }

    /***
	 * Returns the annotations found in all the class' methods
	 **/
    public function getProperties($className ) {

		/**
		 * Get the full annotations from the class
		 */
		$classAnnotations = $this->get(className);

		/**
		 * A valid annotations reflection is an object
		 */
		if ( gettype($classAnnotations) == "object" ) {
			return classAnnotations->getPropertiesAnnotations();
		}

		return [];
    }

    /***
	 * Returns the annotations found in a specific property
	 **/
    public function getProperty($className , $propertyName ) {

		/**
		 * Get the full annotations from the class
		 */
		$classAnnotations = $this->get(className);

		/**
		 * A valid annotations reflection is an object
		 */
		if ( gettype($classAnnotations) == "object" ) {
			$properties = classAnnotations->getPropertiesAnnotations();
			if ( gettype($properties) == "array" ) {
				if ( fetch property, properties[propertyName] ) {
					return property;
				}
			}
		}

		/**
		 * Returns a collection anyways
		 */
		return new Collection();
    }

}