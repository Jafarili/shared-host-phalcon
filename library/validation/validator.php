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

    }

    /***
	 * Checks if an option has been defined

	 * @deprecated since 2.1.0
	 * @see \Phalcon\Validation\Validator::hasOption()
	 **/
    public function isSetOption($key ) {

    }

    /***
	 * Checks if an option is defined
	 **/
    public function hasOption($key ) {

    }

    /***
	 * Returns an option in the validator's options
	 * Returns null if the option hasn't set
	 **/
    public function getOption($key , $defaultValue  = null ) {

    }

    /***
	 * Sets an option in the validator
	 **/
    public function setOption($key , $value ) {

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

    }

    /***
	 * Prepares a validation message.
	 **/
    protected function prepareMessage($validation , $field , $type , $option  = message ) {

    }

    /***
	 * Prepares a validation code.
	 **/
    protected function prepareCode($field ) {

    }

}