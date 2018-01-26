<?php


namespace Phalcon\Mvc\Collection;

use Phalcon\Mvc\EntityInterface;
use Phalcon\Mvc\Collection\Exception;


/***
 * Phalcon\Mvc\Collection\Document
 *
 * This component allows Phalcon\Mvc\Collection to return rows without an associated entity.
 * This objects implements the ArrayAccess interface to allow access the object as object->x or array[x].
 **/

class Document {

    /***
	 * Checks whether an offset exists in the document
	 *
	 * @param int index
	 * @return boolean
	 **/
    public function offsetExists($index ) {
		return isset $this->{index};
    }

    /***
	 * Returns the value of a field using the ArrayAccess interfase
	 **/
    public function offsetGet($index ) {
		if ( fetch value, $this->) {index} ) {
			return value;
		}
		throw new Exception("The index does not exist in the row");
    }

    /***
	 * Change a value using the ArrayAccess interface
	 **/
    public function offsetSet($index , $value ) {
		$this->{index} = value;
    }

    /***
	 * Rows cannot be changed. It has only been implemented to meet the definition of the ArrayAccess interface
	 *
	 * @param string offset
	 **/
    public function offsetUnset($offset ) {
		throw new Exception("The index does not exist in the row");
    }

    /***
	 * Reads an attribute value by its name
	 *
	 *<code>
	 *  echo $robot->readAttribute("name");
	 *</code>
	 *
	 * @param string attribute
	 * @return mixed
	 **/
    public function readAttribute($attribute ) {
		if ( fetch value, $this->) {attribute} ) {
			return value;
		}
		return null;
    }

    /***
	 * Writes an attribute value by its name
	 *
	 *<code>
	 *  $robot->writeAttribute("name", "Rosey");
	 *</code>
	 *
	 * @param string attribute
	 * @param mixed value
	 **/
    public function writeAttribute($attribute , $value ) {
		$this->{attribute} = value;
    }

    /***
	 * Returns the instance as an array representation
	 *
	 * @return array
	 **/
    public function toArray() {
		return get_object_vars(this);
    }

}