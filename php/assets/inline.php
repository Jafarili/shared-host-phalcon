<?php


namespace Phalcon\Assets;



/***
 * Phalcon\Assets\Inline
 *
 * Represents an inline asset
 *
 *<code>
 * $inline = new \Phalcon\Assets\Inline("js", "alert('hello world');");
 *</code>
 **/

class Inline {

    protected $_type;

    protected $_content;

    protected $_filter;

    protected $_attributes;

    /***
	 * Phalcon\Assets\Inline constructor
	 *
	 * @param string type
	 * @param string content
	 * @param boolean filter
	 * @param array attributes
	 **/
    public function __construct($type , $content , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Sets the inline's type
	 **/
    public function setType($type ) {

    }

    /***
	 * Sets if the resource must be filtered or not
	 **/
    public function setFilter($filter ) {

    }

    /***
	 * Sets extra HTML attributes
	 **/
    public function setAttributes($attributes ) {

    }

    /***
	 * Gets the resource's key.
	 **/
    public function getResourceKey() {

    }

}