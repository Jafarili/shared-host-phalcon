<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\Annotation;
use Phalcon\Annotations\Exception;


/***
 * Phalcon\Annotations\Collection
 *
 * Represents a collection of annotations. This class allows to traverse a group of annotations easily
 *
 *<code>
 * //Traverse annotations
 * foreach ($classAnnotations as $annotation) {
 *     echo "Name=", $annotation->getName(), PHP_EOL;
 * }
 *
 * //Check if the annotations has a specific
 * var_dump($classAnnotations->has("Cacheable"));
 *
 * //Get an specific annotation in the collection
 * $annotation = $classAnnotations->get("Cacheable");
 *</code>
 **/

class Collection {

    protected $_position;

    protected $_annotations;

    /***
	 * Phalcon\Annotations\Collection constructor
	 *
	 * @param array reflectionData
	 **/
    public function __construct($reflectionData  = null ) {

		if ( gettype($reflectionData) != "null" && gettype($reflectionData) != "array" ) {
			throw new Exception("Reflection data must be an array");
		}

		$annotations = [];
		if ( gettype($reflectionData) == "array" ) {
			foreach ( $reflectionData as $annotationData ) {
				$annotations[] = new Annotation(annotationData);
			}
		}
		$this->_annotations = annotations;
    }

    /***
	 * Returns the number of annotations in the collection
	 **/
    public function count() {
		return count(this->_annotations);
    }

    /***
	 * Rewinds the internal iterator
	 **/
    public function rewind() {
		$this->_position = 0;
    }

    /***
	 * Returns the current annotation in the iterator
	 *
	 * @return \Phalcon\Annotations\Annotation
	 **/
    public function current() {
		if ( fetch annotation, $this->_annotations[this->_position] ) {
			return annotation;
		}
		return false;
    }

    /***
	 * Returns the current position/key in the iterator
	 **/
    public function key() {
		return $this->_position;
    }

    /***
	 * Moves the internal iteration pointer to the next position
	 **/
    public function next() {
		$this->_position++;
    }

    /***
	 * Check if the current annotation in the iterator is valid
	 **/
    public function valid() {
		return isset $this->_annotations[this->_position];
    }

    /***
	 * Returns the internal annotations as an array
	 **/
    public function getAnnotations() {
		return $this->_annotations;
    }

    /***
	 * Returns the first annotation that match a name
	 **/
    public function get($name ) {
		$annotations = $this->_annotations;
		if ( gettype($annotations) == "array" ) {
			foreach ( $annotations as $annotation ) {
				if ( name == annotation->getName() ) {
					return annotation;
				}
			}
		}

		throw new Exception("Collection doesn't have an annotation called '" . name . "'");
    }

    /***
	 * Returns all the annotations that match a name
	 **/
    public function getAll($name ) {

		$found = [],
			annotations = $this->_annotations;
		if ( gettype($annotations) == "array" ) {
			foreach ( $annotations as $annotation ) {
				if ( name == annotation->getName() ) {
					$found[] = annotation;
				}
			}
		}

		return found;
    }

    /***
	 * Check if an annotation exists in a collection
	 **/
    public function has($name ) {

		$annotations = $this->_annotations;
		if ( gettype($annotations) == "array" ) {
			foreach ( $annotations as $annotation ) {
				if ( name == annotation->getName() ) {
					return true;
				}
			}
		}
		return false;
    }

}