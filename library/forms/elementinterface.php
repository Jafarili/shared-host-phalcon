<?php


namespace Phalcon\Forms;

use Phalcon\Forms\Form;
use Phalcon\Validation\MessageInterface;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message\Group;


/***
 * Phalcon\Forms\Element
 *
 * Interface for Phalcon\Forms\Element classes
 **/

interface ElementInterface {

    /***
	 * Sets the parent form to the element
	 **/
    public function setForm($form ); 

    /***
	 * Returns the parent form to the element
	 **/
    public function getForm(); 

    /***
	 * Sets the element's name
	 **/
    public function setName($name ); 

    /***
	 * Returns the element's name
	 **/
    public function getName(); 

    /***
	 * Sets the element's filters
	 *
	 * @param array|string filters
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setFilters($filters ); 

    /***
	 * Adds a filter to current list of filters
	 **/
    public function addFilter($filter ); 

    /***
	 * Returns the element's filters
	 *
	 * @return mixed
	 **/
    public function getFilters(); 

    /***
	 * Adds a group of validators
	 *
	 * @param \Phalcon\Validation\ValidatorInterface[]
	 * @param boolean merge
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function addValidators($validators , $merge  = true ); 

    /***
	 * Adds a validator to the element
	 **/
    public function addValidator($validator ); 

    /***
	 * Returns the validators registered for the element
	 **/
    public function getValidators(); 

    /***
	 * Returns an array of prepared attributes for Phalcon\Tag helpers
	 * according to the element's parameters
	 **/
    public function prepareAttributes($attributes  = null , $useChecked  = false ); 

    /***
	 * Sets a default attribute for the element
	 *
	 * @param string attribute
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setAttribute($attribute , $value ); 

    /***
	 * Returns the value of an attribute if present
	 *
	 * @param string attribute
	 * @param mixed defaultValue
	 * @return mixed
	 **/
    public function getAttribute($attribute , $defaultValue  = null ); 

    /***
	 * Sets default attributes for the element
	 **/
    public function setAttributes($attributes ); 

    /***
	 * Returns the default attributes for the element
	 **/
    public function getAttributes(); 

    /***
	 * Sets an option for the element
	 *
	 * @param string option
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setUserOption($option , $value ); 

    /***
	 * Returns the value of an option if present
	 *
	 * @param string option
	 * @param mixed defaultValue
	 * @return mixed
	 **/
    public function getUserOption($option , $defaultValue  = null ); 

    /***
	 * Sets options for the element
	 **/
    public function setUserOptions($options ); 

    /***
	 * Returns the options for the element
	 **/
    public function getUserOptions(); 

    /***
	 * Sets the element label
	 **/
    public function setLabel($label ); 

    /***
	 * Returns the element's label
	 **/
    public function getLabel(); 

    /***
	 * Generate the HTML to label the element
	 **/
    public function label(); 

    /***
	 * Sets a default value in case the form does not use an entity
	 * or there is no value available for the element in _POST
	 *
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setDefault($value ); 

    /***
	 * Returns the default value assigned to the element
	 *
	 * @return mixed
	 **/
    public function getDefault(); 

    /***
	 * Returns the element's value
	 *
	 * @return mixed
	 **/
    public function getValue(); 

    /***
	 * Returns the messages that belongs to the element
	 * The element needs to be attached to a form
	 *
	 * @return \Phalcon\Validation\Message\Group
	 **/
    public function getMessages(); 

    /***
	 * Checks whether there are messages attached to the element
	 **/
    public function hasMessages(); 

    /***
	 * Sets the validation messages related to the element
	 **/
    public function setMessages($group ); 

    /***
	 * Appends a message to the internal message list
	 **/
    public function appendMessage($message ); 

    /***
	 * Clears every element in the form to its default value
	 **/
    public function clear(); 

    /***
	 * Renders the element widget
	 *
	 * @param array attributes
	 **/
    public function render($attributes  = null ); 

}