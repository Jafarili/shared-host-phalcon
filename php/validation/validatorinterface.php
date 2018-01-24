<?php


namespace Phalcon\Validation;



/***
 * Phalcon\Validation\ValidatorInterface
 *
 * Interface for Phalcon\Validation\Validator
 **/

interface ValidatorInterface {

    /***
	 * Checks if an option is defined
	 **/
    public function hasOption($key ); 

    /***
	 * Returns an option in the validator's options
	 * Returns null if the option hasn't set
	 **/
    public function getOption($key , $defaultValue  = null ); 

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $attribute ); 

}