<?php


namespace Phalcon\Validation;

use Phalcon\Validation;
use Phalcon\Validation\Exception;
use Phalcon\Validation\ValidatorInterface;


/***
 * Phalcon\Validation\Validator
 *
 * This is a base class for validators
 **/

abstract class Validator {

    protected $_options;

    /***
	 * Phalcon\Validation\Validator constructor
	 **/
    public function __construct($options  = null ) {
		$this->_options = options;
    }

    /***
	 * Checks if an option has been defined

	 * @deprecated since 2.1.0
	 * @see \Phalcon\Validation\Validator::hasOption()
	 **/
    public function isSetOption($key ) {
		return isset $this->_options[key];
    }

    /***
	 * Checks if an option is defined
	 **/
    public function hasOption($key ) {
		return isset $this->_options[key];
    }

    /***
	 * Returns an option in the validator's options
	 * Returns null if the option hasn't set
	 **/
    public function getOption($key , $defaultValue  = null ) {
		$options = $this->_options;

		if ( gettype($options) == "array" ) {
			if ( fetch value, options[key] ) {
				/*
				 * If we have attribute it means it's Uniqueness validator, we
				 * can have here multiple fields, so we need to check it
				 */
				if ( key == "attribute" && gettype($value) == "array" ) {
					if ( fetch fieldValue, value[key] ) {
						return fieldValue;
					}
				}
				return value;
			}
		}

		return defaultValue;
    }

    /***
	 * Sets an option in the validator
	 **/
    public function setOption($key , $value ) {
		$this->_options[key] = value;
    }

    /***
	 * Executes the validation
	 **/
    abstract public function validate($validation , $attribute ) {

    }

    /***
	 * Prepares a label for the field.
	 **/
    protected function prepareLabel($validation , $field ) {

		$label = $this->getOption("label");
		if ( gettype($label) == "array" ) {
			$label = label[field];
		}

		if ( empty label ) {
			$label = validation->getLabel(field);
		}

		return label;
    }

    /***
	 * Prepares a validation message.
	 **/
    protected function prepareMessage($validation , $field , $type , $option  = message ) {

		$message = $this->getOption(option);
		if ( gettype($message) == "array" ) {
			$message = message[field];
		}

		if ( empty message ) {
			$message = validation->getDefaultMessage(type);
		}

		return message;
    }

    /***
	 * Prepares a validation code.
	 **/
    protected function prepareCode($field ) {

		$code = $this->getOption("code");
		if ( gettype($code) == "array" ) {
			$code = code[field];
		}

		return code;
    }

}