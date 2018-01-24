<?php


namespace Phalcon\Validation;

use Phalcon\Validation\Message;


/***
 * Phalcon\Validation\Message
 *
 * Interface for Phalcon\Validation\Message
 **/

interface MessageInterface {

    /***
	 * Sets message type
	 **/
    public function setType($type ); 

    /***
	 * Returns message type
	 **/
    public function getType(); 

    /***
	 * Sets verbose message
	 **/
    public function setMessage($message ); 

    /***
	 * Returns verbose message
	 *
	 * @return string
	 **/
    public function getMessage(); 

    /***
	 * Sets field name related to message
	 **/
    public function setField($field ); 

    /***
	 * Returns field name related to message
	 *
	 * @return string
	 **/
    public function getField(); 

    /***
	 * Magic __toString method returns verbose message
	 **/
    public function __toString(); 

    /***
	 * Magic __set_state helps to recover messages from serialization
	 **/
    public static function __set_state($message ); 

}