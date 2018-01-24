<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\Model\Message;


/***
 * Phalcon\Mvc\Model\Validator
 *
 * This is a base class for Phalcon\Mvc\Model validators
 *
 * This class is only for backward compatibility reasons to use with Phalcon\Mvc\Collection.
 * Otherwise please use the validators provided by Phalcon\Validation.
 *
 * @deprecated 3.1.0
 * @see Phalcon\Validation\Validator
 **/

abstract class Validator {

    protected $_options;

    protected $_messages;

    /***
	 * Phalcon\Mvc\Model\Validator constructor
	 **/
    public function __construct($options ) {

    }

    /***
	 * Appends a message to the validator
	 *
	 * @param string message
	 * @param string|array field
	 * @param string type
	 **/
    protected function appendMessage($message , $field  = null , $type  = null ) {

    }

    /***
	 * Returns messages generated by the validator
	 **/
    public function getMessages() {

    }

    /***
	 * Returns all the options from the validator
	 *
	 * @return array
	 **/
    public function getOptions() {

    }

    /***
	 * Returns an option
	 **/
    public function getOption($option , $defaultValue  =  ) {

    }

    /***
	 * Check whether an option has been defined in the validator options
	 **/
    public function isSetOption($option ) {

    }

}