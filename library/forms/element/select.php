<?php


namespace Phalcon\Forms\Element;

use Phalcon\Tag\Select;
use Phalcon\Forms\Element;


/***
 * Phalcon\Forms\Element\Select
 *
 * Component SELECT (choice) for forms
 **/

class Select extends Element {

    protected $_optionsValues;

    /***
	 * Phalcon\Forms\Element constructor
	 *
	 * @param string name
	 * @param object|array options
	 * @param array attributes
	 **/
    public function __construct($name , $options  = null , $attributes  = null ) {
		$this->_optionsValues = options;
		parent::__construct(name, attributes);
    }

    /***
	 * Set the choice's options
	 *
	 * @param array|object options
	 * @return \Phalcon\Forms\Element
	 **/
    public function setOptions($options ) {
		$this->_optionsValues = options;
		return this;
    }

    /***
	 * Returns the choices' options
	 *
	 * @return array|object
	 **/
    public function getOptions() {
		return $this->_optionsValues;
    }

    /***
	 * Adds an option to the current options
	 *
	 * @param array option
	 * @return this
	 **/
    public function addOption($option ) {

		if ( gettype($option) == "array" ) {
			foreach ( key, $option as $value ) {
				$this->_optionsValues[key] = value;
			}
		} else {
			$this->_optionsValues[] = option;
		}

		return this;
    }

    /***
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 **/
    public function render($attributes  = null ) {
		return Select::selectField(this->prepareAttributes(attributes), $this->_optionsValues);
    }

}