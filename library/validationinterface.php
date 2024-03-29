<?php


namespace Phalcon;

use Phalcon\Di\Injectable;
use Phalcon\Validation\Exception;
use Phalcon\Validation\MessageInterface;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\ValidatorInterface;


/***
 * Phalcon\ValidationInterface
 *
 * Interface for the Phalcon\Validation component
 **/

interface ValidationInterface {

    /***
	 * Validate a set of data according to a set of rules
	 *
	 * @param array|object data
	 * @param object entity
	 * @return \Phalcon\Validation\Message\Group
	 **/
    public function validate($data  = null , $entity  = null ); 

    /***
	 * Adds a validator to a field
	 **/
    public function add($field , $validator ); 

    /***
	 * Alias of `add` method
	 **/
    public function rule($field , $validator ); 

    /***
	 * Adds the validators to a field
	 **/
    public function rules($field , $validators ); 

    /***
	 * Adds filters to the field
	 *
	 * @param string field
	 * @param array|string filters
	 * @return \Phalcon\Validation
	 **/
    public function setFilters($field , $filters ); 

    /***
	 * Returns all the filters or a specific one
	 *
	 * @param string field
	 * @return mixed
	 **/
    public function getFilters($field  = null ); 

    /***
	 * Returns the validators added to the validation
	 **/
    public function getValidators(); 

    /***
	 * Returns the bound entity
	 *
	 * @return object
	 **/
    public function getEntity(); 

    /***
	 * Adds default messages to validators
	 **/
    public function setDefaultMessages($messages ); 

    /***
	 * Get default message for validator type
	 *
	 * @param string type
	 **/
    public function getDefaultMessage($type ); 

    /***
	 * Returns the registered validators
	 **/
    public function getMessages(); 

    /***
	 * Adds labels for fields
	 **/
    public function setLabels($labels ); 

    /***
	 * Get label for field
	 *
	 * @param string field
	 * @return string
	 **/
    public function getLabel($field ); 

    /***
	 * Appends a message to the messages list
	 **/
    public function appendMessage($message ); 

    /***
	 * Assigns the data to an entity
	 * The entity is used to obtain the validation values
	 *
	 * @param object entity
	 * @param array|object data
	 * @return \Phalcon\Validation
	 **/
    public function bind($entity , $data ); 

    /***
	 * Gets the a value to validate in the array/object data source
	 *
	 * @param string field
	 * @return mixed
	 **/
    public function getValue($field ); 

}