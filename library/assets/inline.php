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
		$this->_type = type,
			this->_content = content,
			this->_filter = filter;
		if ( gettype($attributes) == "array" ) {
			$this->_attributes = attributes;
		}
    }

    /***
	 * Sets the inline's type
	 **/
    public function setType($type ) {
		$this->_type = type;
		return this;
    }

    /***
	 * Sets if the resource must be filtered or not
	 **/
    public function setFilter($filter ) {
		$this->_filter = filter;
		return this;
    }

    /***
	 * Sets extra HTML attributes
	 **/
    public function setAttributes($attributes ) {
		$this->_attributes = attributes;
		return this;
    }

    /***
	 * Gets the resource's key.
	 **/
    public function getResourceKey() {

		$key = $this->getType() . ":" . $this->getContent();

		return md5(key);
    }

}